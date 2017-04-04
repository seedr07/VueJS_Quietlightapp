<?php
 
 
// files storage folder
$dir = __DIR__ ;
$dir = str_replace("scripts","",$dir);
$dir = $dir."uploads/";
 
$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
 
if ($_FILES['file']['type'] == 'image/png'
		|| $_FILES['file']['type'] == 'image/jpg'
		|| $_FILES['file']['type'] == 'image/gif'
		|| $_FILES['file']['type'] == 'image/jpeg'
		|| $_FILES['file']['type'] == 'image/pjpeg'
        || $_FILES['file']['type'] == 'application/pdf')
{

    $extension = "";
    switch($_FILES['file']['type']){
        case $_FILES['file']['type'] == 'image/png':
            $extension = '.png';
            break;
        case $_FILES['file']['type'] == 'image/gif':
            $extension = ".gif";
            break;
        case $_FILES['file']['type'] == 'image/jpg':
        case $_FILES['file']['type'] == 'image/jpeg':
        case $_FILES['file']['type'] == 'image/pjpeg':
            $extension = ".jpg";
            break;
        case $_FILES['file']['type'] == 'application/pdf':
            $extension = ".pdf";
            break;
    }

    // setting file's mysterious name
    $filename = md5(date('YmdHis')).$extension;
    $file = $dir.$filename;
 
    // copying
    move_uploaded_file($_FILES['file']['tmp_name'], $file);
 
    // displaying file
    $array = array(
        'filelink' => 'uploads/'.$filename
    );
 
    echo stripslashes(json_encode($array));
 
}
 
?>