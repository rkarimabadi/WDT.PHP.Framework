<?php

class Using {
    private static $imports = array(),$models = array();
    
    public static function Import($path) {
    	\array_push(self::$imports, $path);
    	$version = '?cache='.App_Cache;
    	if(strpos($path,'.css') > 0)  echo '<link href="'.$path.$version.'" rel="stylesheet"/>';
    	elseif(strpos($path,'.js') > 0)  echo '<script src="'.$path.$version.'" type="text/javascript"></script>';
    }
    public static function Asset($path) {self::Import(Root_Http.'Assets/'.$path);}
    public static function Area($path) {self::Import(Root_Http.'Areas/'.This_Area.'/'.$path);}
    public static function GlobalFont($path) {self::Import(Root_Http.'Fonts/'.$path);}
    public static function GlobalContent($path) {self::Import(Root_Http.'Contents/'.$path);}
    public static function GlobalScript($path) {self::Import(Root_Http.'Scripts/'.$path);}
    public static function Content($path) {self::Import(Root_Http.'Areas/'.This_Area.'/Views/'.This_Action.'/Contents/'.$path);}
    public static function Script($path) {self::Import(Root_Http.'Areas/'.This_Area.'/Views/'.This_Action.'/Scripts/'.$path);}

    public static function Head() {
        if(file_exists(Root_Contents.'style.css')) self::GlobalContent('style.css');
        if(This_Area != null) 
        {
        	if(file_exists(Root_Areas.This_Area.'/Contents/style.css')) self::Area('Contents/style.css');
        	if(file_exists(Root_Areas.This_Area.'/Contents/'.This_Controller.'.css')) self::Area('Contents/'.This_Controller.'.css');
        	if(file_exists(This_Folder.'Contents/style.css')) self::Area('Views/'.This_Controller.'/Contents/style.css');
        	if(file_exists(This_Folder.'Contents/'.This_Action.'.css')) self::Area('Views/'.This_Controller.'/Contents/'.This_Action.'.css');
        } 
        else 
        {
        	if(file_exists(Root_Contents.This_Controller.'.css')) self::Content(This_Controller.'.css');
        	if(file_exists(This_Folder.'Contents/style.css')) self::Import(Root_Http.'Views/'.This_Controller.'/Contents/style.css');
        	if(file_exists(This_Folder.'Contents/'.This_Action.'.css')) self::Import(Root_Http.'Views/'.This_Controller.'/Contents/'.This_Action.'.css');
        }
    }
    public static function Foot() {
        if(file_exists(Root_Scripts.'script.js')) self::GlobalScript('script.js');
        if(This_Area != null) 
        {
	        if(file_exists(Root_Areas.This_Area.'/Scripts/script.js')) self::Area('Scripts/script.js');
	        if(file_exists(Root_Areas.This_Area.'/Scripts/'.This_Controller.'.js')) self::Area('Scripts/'.This_Controller.'.js');
	        if(file_exists(This_Folder.'Scripts/script.js')) self::Area('Views/'.This_Controller.'/Scripts/script.js');
	        if(file_exists(This_Folder.'Scripts/'.This_Action.'.js')) self::Area('Views/'.This_Controller.'/Scripts/'.This_Action.'.js');
        }
        else
        {
        	if(file_exists(Root_Scripts.This_Controller.'.js')) self::GlobalScript(This_Controller.'.js');
        	if(file_exists(This_Folder.'Contents/script.js')) self::Import(Root_Http.'Views/'.This_Controller.'/Contents/script.js');
        	if(file_exists(This_Folder.'Contents/'.This_Action.'.js')) self::Import(Root_Http.'Views/'.This_Controller.'/Contents/'.This_Action.'.js');
        }
    }
}