<?php
# If there is a loving god, I'll never have to touch this again 
/**
 * Returns the response of the authorization endpoint for backblaze. Has the bucket id and key built-in. Nothing needs to be provided.
 */
function backblaze_authorize()
{
	$auth_key = array(
		'Authorization: Basic MDAyN2MxYjE0ZGVkMWRmMDAwMDAwMDAwMzpLMDAyVEhLK1VYaXltWXlrSEdsT0RyU01TWkJmZEx3'
	);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.backblazeb2.com/b2api/v2/b2_authorize_account',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => $auth_key
	));
	$response = curl_exec($curl);
	$response = json_decode($response, true);
	curl_close($curl);
	return $response;
}



/**
 * Returns a array of files, including their names, content hashes, and other information. Files are not organized 
 * by folder, but rather include a faux path in their name. It can be dissected by exploding on '/'. 
 * @param string $apiUrl The url to the api endpoint, has a chance to change, so it's best to dynamically provide it via the auth endpoint. Should be in session if BB was init'ed right
 * @param string $bucketId The bucketId for the bucket. Dynamic for when chris asks to add support for other buckets.  
 */
function backblaze_file_list($apiUrl, $bucketId)
{
	if (!isset($_SESSION['BB_FILE_LIST']) || $_SESSION['BB_FILE_LIST_TIME'] > time() + (60 * 60) || isset($_GET['recache'])) {
		$_SESSION['BB_CACHE_STATUS'] = false;
		$curl = curl_init();
		$getHeaders = array(
			"bucketId" => $bucketId,
			"maxFileCount" => 10000
		);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $apiUrl . '/b2api/v2/b2_list_file_names?' . http_build_query($getHeaders),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				"Authorization: {$_SESSION['BB_AUTH_TOKEN']}"
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$_SESSION['BB_FILE_LIST'] = $response;
		$_SESSION['BB_FILE_LIST_TIME'] = time();
	} else {
		$_SESSION['BB_CACHE_STATUS'] = true;
		$response = $_SESSION['BB_FILE_LIST'];
	}
	return json_decode($response, true);
}

function backblaze_file_tree($files, $folderFilter = "")
{
	$tree = array();
	foreach ($files as $id => $file) {
		$folder = array_slice(explode("/", $file['fileName']), 0, count(explode("/", $folderFilter)));
		if ($folderFilter == "" || implode("/", $folder) == $folderFilter) {
			//$file includes the file elements
			$tree = array_merge_recursive($tree, backblaze_file_array($file));
		}
	}
	return $tree;
}


function backblaze_file_array($file, $depth = 0)
{
	//Get array of path by exploding on /
	$path = explode("/", $file['fileName']);
	//Pop Filename
	$fileName = array_pop($path);
	$result = [$fileName => $file];
	for ($i = count($path) - 1; $i >= $depth; $i--) {
		$result = [implode("/", $path) => $result];
		array_pop($path);
	}
	return $result;
}

function idencode($string)
{
	$string = str_replace(array(" ", "%", "&", "+", "/"), "", $string);
	return $string;
}


//Returns the directory listing from a list of files.
function backblaze_tree_HTML($fileList, $folder, $uld="")
{
	$html = "";
	foreach ($fileList as $fileName => $file) {
		$cleanFileName = ltrim($fileName, "/");
		$cleanFileUrl = urlencode($cleanFileName);
		if (!isset($file['fileName'])) { //Check if it has attributes. Folders do not, as they just contain the list of files
			$path = explode("/", $fileName);
			$pathString = idencode(implode("/", $path));
			$depth = substr_count($fileName, "/");
			$folderDepth = $folder == "" ? 0 : substr_count($folder, "/") + 1;
			// if ($depth == $folderDepth) {
				$path = explode("/", $fileName);
				$fileName = array_pop($path);
				$cleanCSSID = idencode($fileName);
				$onClickDropdown = <<<ONCLICK
                toggleFiles("$cleanCSSID");
ONCLICK;
				$dropdownArrow = <<<ARROW
                <td><img class="backblaze-folder-dropdown" onclick='$onClickDropdown' src="/images/classy/16x16/folder.png"></img></td>
ARROW;
				$onClickFolder = <<<FOLDER
                window.location.href = window.location.href.split("?")[0] + "?folder=$cleanFileUrl";
FOLDER;
			if ($depth == $folderDepth) { $display = 'auto'; } else { $display = 'none'; }
				$html .= <<<FOLDERROW
                <tr class="backblaze-file-browser-row backblaze-folder $uld" style='display: $display' id='$fileName'>
				<td><div style="float: left;" onclick='$onClickFolder'>$cleanFileName</div></td>
                <td></td>
                $dropdownArrow
                </tr>
FOLDERROW;
			//If file is a folder, should run the function again.
			$html .= backblaze_tree_HTML($file, $folder, $cleanCSSID);
		} else { // 		  [  ^   FOLDER   ]	 /	[ 	FILE 	v ]
			$path = explode("/", $file['fileName']);
			$fileDepth = substr_count($file['fileName'], "/");
			//+1 because the first level directories do not contain /, but they are at level one. Also checks if at / and sets depth to 0
			$folderDepth = $folder == "" ? 0 : substr_count($folder, "/") + 1;
			if ($fileDepth == $folderDepth) {
				$display = "auto";
			} else {
				$display = "none";
			}
			$fileName = array_pop($path);
			$fileID = $file['fileId'];
			$pathString = idencode(implode("/", $path));
			$fileSize = $file['contentLength'];
			$fileUrl = "https://www.1si.biz/public/" . implode('/', array_map('urlencode', explode('/', $file['fileName'])));
			$fileSizeString = fileSizeString($fileSize);
			$downloadString = "{$_SESSION['BB_AUTH_URL']}/b2api/v2/b2_download_file_by_id?fileId=$fileID";
			$onClickPreview = <<<DOWNLOAD
                window.open("$downloadString", "_blank");
DOWNLOAD;
			$html .= <<<FILEROW
            <tr class='backblaze-file-browser-row backblaze-file $pathString' id='$fileName' style='display: $display;'>
            <td class='backblaze-file-name'> $fileName </td>
            <td class='backblaze-file-size'>$fileSizeString</td>
            <td class='backblaze-file-options'>
			<img src='/images/link.svg' class='backblaze-file-option clip' height=16 width=16 data-clipboard-text='$fileUrl' onclick=''></img>
				<img src='/images/classy/16x16/page_search.png' class='backblaze-file-option' onclick='$onClickPreview'></img>&nbsp;
				<img src='/images/classy/16x16/page.png' class='backblaze-file-option' onclick='download("$downloadString", "$fileName")'></img>&nbsp;
			</td>
            </tr>
FILEROW;
		}
	}
	return $html;
}
//
//Constructs the layout from the current folder and current files, and returns the HTML string for the final rendering
function backblaze_file_browser()
{
	$folder = urldecode($_SESSION["BB_FOLDER"]);
	$files = backblaze_file_list($_SESSION['BB_AUTH_URL'], $_SESSION['BB_BUCKET_ID']);
	$recacheAction = 'window.location.href = window.location.href.split("?")[0] + "?recache=yeboi"';
	$cacheStatus = $_SESSION['BB_CACHE_STATUS'] ? "Old Data" : "New Data";
	$cacheStatus .= "<img src='/images/classy/16x16/repeat.png' style='vertical-align:middle; float: right;' onclick='$recacheAction' ></img>";
	$parentDir = explode("/", $folder);
	array_pop($parentDir);
	$parentDir = implode("/", $parentDir);
	$html = "<div class='backblaze-file-browser'><table cellspacing=0 style='width: 100%'>";
	$html .= "
			<thead> 
				<tr> 
					<td colspan=2 class='backblaze-header-text'>$folder</td>
					<td class='backblaze-header-text'>$cacheStatus</td>
				</tr>";
	$html .= "<tr>
				<td>Name</td>
				<td style='width:50px;'>Size</td>
				<td style='width:100px;'>Options</td>
			  </tr>";
	$html .= "</thead>";

	//Top level directory button
	$html .= <<<BASE
  <tr><td colspan=3 class='backblaze-file-browser-row backblaze-folder' id='backblaze-file-browser-dirTop' onclick='
    window.location.href = window.location.href.split("?")[0] + "?folder=";
  '> . </td></tr>
BASE;

	//Upper level directory button
	$html .= <<<BASE
  <tr><td colspan=3 class='backblaze-file-browser-row backblaze-folder' id='backblaze-file-browser-dirUp' onclick='
    window.location.href = window.location.href.split("?")[0] + "?folder=$parentDir";
  '> .. </td></tr>
BASE;
	return $html . backblaze_tree_HTML(backblaze_file_tree($files['files'], $folder), $folder) . "</table></div>";
}


$hour = 60 * 60;
//If auth isn't set or it's expired
if (!isset($_SESSION['BB_AUTH_TOKEN']) || time() >= $_SESSION["BB_AUTH_TIME"] + ($hour * 2)) {
	$authorization = backblaze_authorize();
	$_SESSION['BB_AUTH_TOKEN'] = $authorization["authorizationToken"];
	$_SESSION['BB_AUTH_TIME'] = time();
	$_SESSION['BB_AUTH_URL'] = $authorization["apiUrl"];
	$_SESSION['BB_BUCKET_ID'] = "67dcc1ebd1d45dae7db10d1f";
}
if (isset($_GET['folder'])) {
	$_SESSION['BB_FOLDER'] = $_GET['folder'];
} elseif (!isset($_SESSION['BB_FOLDER'])) {
	$_SESSION['BB_FOLDER'] = "";
}
?>
<style>
	.dropdown {
		float: right;
	}
</style>
<script>
	function toggleFiles(className) {
		$('[class*="' + className + '"]').css("display") == "none" ? $('[class*="' + className + '"]').css("display", "table-row") : $('[class*="' + className + '"]').css("display", "none");
	}

	function download(file, filename) {
		fetch(file)
			.then(resp => resp.blob())
			.then(blob => {
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.style.display = 'none';
				a.href = url;
				// the filename you want
				a.download = filename;
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				delete a;
			})
			.catch((e) => alert(e));
	}
	var clipboard = new ClipboardJS('.clip');
	clipboard.on('success', function(e) {
		alert('Copied to clipboard');
	});

	clipboard.on('error', function(e) {
		alert('Failed to copy');
	});


</script>