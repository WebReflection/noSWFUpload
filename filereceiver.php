<?php

// iframe creation, just an empty page
if(isset($_GET['AjaxUploadFrame']))
    exit;

// populate in a fast and completely unobtrusive way the super global
// $_FILES variable if the browser sent file via Ajax and without boundary
// to use only if you want to support Safari 4 beta
require 'noswfupload.php';

// directory used to store files
$uploadFolder   = 'upload';

if(
    isset($_FILES['test']) && (

        // normal sent file
        move_uploaded_file($_FILES['test']['tmp_name'], $uploadFolder.DIRECTORY_SEPARATOR.$_FILES['test']['name']) ||

        // files created via noswfupload.php (Safari 4 beta)
        copy($_FILES['test']['tmp_name'], $uploadFolder.DIRECTORY_SEPARATOR.$_FILES['test']['name'])
    )
){
    // in copy case, Safari 4 beta, files will not be removed, do it manually
    if(file_exists($_FILES['test']['tmp_name']))
        unlink($_FILES['test']['tmp_name']);

    // upload completed
    exit('OK');
}

// if something was wrong ... should generate onerror event
header('HTTP/1.1 500 Internal Server Error');

?>