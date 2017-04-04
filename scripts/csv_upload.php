<?php
 
 
// files storage folder
$dir = __DIR__ ;
$dir = str_replace("scripts","",$dir);
$dir = $dir."uploads/";

$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

if ($_FILES['file']['type'] == 'text/csv'
        || $_FILES['file']['type'] == 'text/plain'
		|| $_FILES['file']['type'] == 'application/csv'
		|| $_FILES['file']['type'] == 'application/vnd.ms-excel')
{

    $extension = ".csv";
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
else {
	echo "Not CSV format.";
}
 
?>