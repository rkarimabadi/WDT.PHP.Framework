<?php
$dirs = array('Http','Mvc');
foreach($dirs as $dir) {
    $files = scandir($dir);
    foreach($files as $file) {
        if(in_array($file,array('.','..'))) continue;
        include $dir.'/'.$file;
    }
}
?>