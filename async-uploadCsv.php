<?php
$path = "uploads/";

if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
    $name = $_FILES['file']['name'];
    if(strlen($name)) {
        $fileName = time()."_".$name;
        $tmp = $_FILES['file']['tmp_name'];
        move_uploaded_file($tmp, $path.$fileName);
        echo $fileName;
    }
    else
        echo "";
    exit;
}
