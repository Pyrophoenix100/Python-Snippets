<?php
function alecDebug($call, $elsecall = "") {
  if ($_SESSION['user_displayname'] == "Alec" && is_callable($call)) {
      $call();
  } elseif (is_callable($elsecall)) {
      $elsecall();
  }
}

function alert($message) {
  echo ("<script> alert('$message'); </script>");
}

function fileSizeString($fileSize) {
  $fileSizeString = $fileSize . "B";
  if ($fileSize > 1000) {
    $fileSizeString = number_format(($fileSize / 1000), 0) . 'KB';
  }
  if ($fileSize > 1000000) {
    $fileSizeString = number_format(($fileSize / 1000000), 0) . 'MB';
  }
  if ($fileSize > 1000000000) {
    $fileSizeString = number_format(($fileSize / 1000000000), 0) . 'GB';
  }
  return $fileSizeString;
}

function option($optionName, $options, $default=false) {
  if (in_array($optionName, array_keys($options)) && $options[$optionName] != false) {
      if ($options[$optionName] == 'on') {
          return true;
      } else { return $options[$optionName]; }
  } else { return $default; }
}
function safeAccess(&$string) { 
  if (isset($string)) {
      return $string;
  } else {
      return '';
  }
}

function getCRMIDfromCustomerID ($customerId) {
  global $thedbihandle;
  $row = mysqli_fetch_array(mysqli_query($thedbihandle, "SELECT * FROM 1si_data.1si_custlogin WHERE customerid = '$customerId' LIMIT 1"));
  #RESET SESSION VARS AS WE WILL HAVE A GUARENTEED CLEAN CRMID
  $_SESSION['custcrmid']           = $row['crmid'];
  $_SESSION['cust_customercrmid']  = $row['crmid'];
  return $row['crmid'];
}

?>