<?php
include 'Path.php';
$dirs = array('Http','Mvc');
foreach($dirs as $dir) {
    $files = scandir(Root_Resources.$dir);
    foreach($files as $file) {
        if(in_array($file,array('.','..'))) continue;
        include Root_Resources.$dir.'/'.$file;
    }
}
?>