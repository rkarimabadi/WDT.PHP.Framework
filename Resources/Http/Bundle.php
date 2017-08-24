<?php

class Bundle {
    protected static $bundles = array();
    private static $imports = array(),$models = array();

    public static function Add($name,$url) {
        if(!isset(Bundle::$bundles[$name])) Bundle::$bundles[$name] = array();
        array_push(Bundle::$bundles[$name],$url);
    }
    public static function Render($name) {
        if(isset(Bundle::$bundles[$name])) {
            foreach(Bundle::$bundles[$name] as $url) {
                Bundle::Import($url);
            }
        }
    }
    public static function Import($path) {
    	\array_push(self::$imports, $path);
    	$version = '?cache='.App_Cache;
    	if(strpos($path,'.css') > 0)  echo '<link href="'.$path.$version.'" rel="stylesheet"/>';
    	elseif(strpos($path,'.js') > 0)  echo '<script src="'.$path.$version.'" type="text/javascript"></script>';
    }
}
?>