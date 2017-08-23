<?php

class Bundle {
    protected static $bundles = array();

    public static function Add($name,$url) {
        if(!isset(Bundle::$bundles[$name])) Bundle::$bundles[$name] = array();
        array_push(Bundle::$bundles[$name],$url);
    }
    public static function Render($name) {
        if(isset(Bundle::$bundles[$name])) {
            foreach(Bundle::$bundles[$name] as $url) {
                Using::Import($url);
            }
        }
    }
}
?>