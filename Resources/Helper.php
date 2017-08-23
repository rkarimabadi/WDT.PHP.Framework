<?php 
function getval($name) {return (isset($_GET[$name]) ? $_GET[$name] : null);}

function postval($name) {return (isset($_POST[$name]) ? $_POST[$name] : null);}
function postname($name) {return 'name="'.$name.'" value="'.postval($name).'"';}
function postselect($name,$value) {return (postval($name) == $value ? 'selected' : '');}

function valuename($name,$value) {return 'name="'.$name.'" value="'.$value.'"';}
function valueselect($name,$value1,$value2) {return ($value2 == $value1 ? 'selected' : '');}
?>