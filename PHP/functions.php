<?php
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
?>