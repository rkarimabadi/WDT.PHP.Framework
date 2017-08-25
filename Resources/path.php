<?php
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\','/',realpath('').'/'));
define('Dir_Sep',DIRECTORY_SEPARATOR);
define('Root_Http',($root[0] == '/' ? $root : '/'.$root));
define('Root', realpath(''). DIRECTORY_SEPARATOR);

define('Root_Areas_Http',Root_Http.'Areas/');
define('Root_Areas',Root.'Areas'.Dir_Sep);
define('Root_Assets_Http',Root_Http.'Assets/');
define('Root_Assets',Root.'Assets'.Dir_Sep);
define('Root_Contents_Http',Root_Http.'Contents/');
define('Root_Contents',Root.'Contents'.Dir_Sep);
define('Root_Controllers_Http',Root_Http.'Controllers/');
define('Root_Controllers',Root.'Controllers'.Dir_Sep);
define('Root_Fonts_Http',Root_Http.'Fonts/');
define('Root_Fonts',Root.'Fonts'.Dir_Sep);
define('Root_Images_Http',Root_Http.'Images/');
define('Root_Images',Root.'Images'.Dir_Sep);
define('Root_Layouts_Http',Root_Http.'Layouts/');
define('Root_Layouts',Root.'Layouts'.Dir_Sep);
define('Root_Models_Http',Root_Http.'Models/');
define('Root_Models',Root.'Models'.Dir_Sep);
define('Root_Resources_Http',Root_Http.'Resources/');
define('Root_Resources',Root.'Resources'.Dir_Sep);
define('Root_Scripts_Http',Root_Http.'Scripts/');
define('Root_Scripts',Root.'Scripts'.Dir_Sep);
define('Root_Views_Http',Root_Http.'Views/');
define('Root_Views',Root.'Views'.Dir_Sep);

$request = $_SERVER['REQUEST_URI'];
$hasquery = strpos($request, '?');
if(is_int($hasquery)) $request = substr($request,0,$hasquery);
$url = str_replace(Root_Http, '', urldecode($request));
$url = explode('/', $url);
$parts = array();
for($i = 0,$length = count($url);$i < $length;$i++) if(strlen($url[$i]) > 0) array_push($parts,$url[$i]);
unset($url);

define('Default_Area','__');
define('Default_Controller','Home');
define('Default_Action','Index');

$count = count($parts);
$area = ($count > 0 ? $parts[0] : Default_Area);
$controller = Default_Controller;
$action = Default_Action;

$parameters = array();
if(file_exists(Root_Areas.$area)) {
    if($count > 1) $controller = $parts[1];
    if($count > 2) $action = $parts[2];
    
    for($i = 3;$i < $count;$i++) array_push($parameters,$parts[$i]);
} else {
    $area = null;
    if($count > 0) $controller = $parts[0];
    if($count > 1) $action = $parts[1];
    for($i = 2;$i < $count;$i++) array_push($parameters,$parts[$i]);
}

define('This_Area',$area);
define('This_Area_Http',Root_Http.(This_Area != null ? This_Area.'/' : ''));
define('This_Controller',$controller);
define('This_Controller_Http',This_Area_Http.This_Controller.'/');
define('This_Action',$action);
define('This_Action_Http',This_Controller_Http.This_Action.'/');

define('This_Root',(This_Area == null ? Root : Root_Areas.This_Area.Dir_Sep));
define('This_Root_Http',(This_Area == null ? Root_Http : Root_Areas_Http.This_Area.'/'));
define('This_Contents_Http',This_Root_Http.'Contents/');
define('This_Contents',This_Root.'Contents'.Dir_Sep);
define('This_Controllers_Http',This_Root_Http.'Controllers/');
define('This_Controllers',This_Root.'Controllers'.Dir_Sep);
define('This_Fonts_Http',This_Root_Http.'Fonts/');
define('This_Fonts',This_Root.'Fonts'.Dir_Sep);
define('This_Images_Http',This_Root_Http.'Images/');
define('This_Images',This_Root.'Images'.Dir_Sep);
define('This_Layouts_Http',This_Root_Http.'Layouts/');
define('This_Layouts',This_Root.'Layouts'.Dir_Sep);
define('This_Models_Http',This_Root_Http.'Models/');
define('This_Models',This_Root.'Models'.Dir_Sep);
define('This_Scripts_Http',This_Root_Http.'Scripts/');
define('This_Scripts',This_Root.'Scripts'.Dir_Sep);
define('This_Views_Http',This_Root_Http.'Views/');
define('This_Views',This_Root.'Views'.Dir_Sep);

define('This_Folder_Http',This_Views_Http.This_Controller.'/');
define('This_Folder',This_Views.This_Controller.Dir_Sep);

function spl_autoload_register_func($namespace) 
{
    if(!class_exists($namespace)) {
        $namespace = str_replace('\\','/',$namespace);
        $parts = explode('/',$namespace);
        $first = $parts[0];
        $last = $parts[count($parts) - 1];
        if (file_exists(This_Models.$last.'.php')) include This_Models.$last.'.php';
        elseif (file_exists(Root_Areas.$first.'/Models/'.$last.'.php')) include Root_Areas.$first.'/Models/'.$last.'.php';
        elseif (file_exists(Root.$namespace.'.php')) include Root.$namespace.'.php';
    }
}
spl_autoload_register('spl_autoload_register_func');