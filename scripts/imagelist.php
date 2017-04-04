<?php
 
 
// files storage folder
$dir = __DIR__."/../userfiles/";
$webroot = "/userfiles/";

$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) {
   if($filename != "." && $filename != "..") $files[] = ["image"=>$webroot.$filename, "title"=>$webroot.$filename, "thumb" => $webroot.$filename];
}

sort($files);

echo json_encode($files);

?>