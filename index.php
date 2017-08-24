<?php
ini_set('display_errors', 'On');

include 'Resources/_include.php';
include 'Bundles.php';
include 'Session.php';

use \Resources\Mvc\Controller;

if(file_exists(Root_Areas.$area.'/Controllers/'.$controller.'Controller.php')) include Root_Areas.$area.'/Controllers/'.$controller.'Controller.php';
elseif(file_exists(Root_Controllers.$controller.'Controller.php')) include Root_Controllers.$controller.'Controller.php';

$controller = (This_Area == null ? 'Controllers' : This_Area.'\Controllers').'\\'.This_Controller.'Controller';
if (class_exists($controller)) {
    $controller = new $controller($parameters);
} else {
    if (Err_Controller == null) echo 'Controller ['.This_Controller.'] not exists';
    else {
        ob_end_clean();
        header('Location: '.Err_Controller);
    };
}