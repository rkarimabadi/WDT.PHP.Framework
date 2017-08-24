<?php

abstract class Layout {
    private static $body = '';
    public static $buffers = array();
    private static $sectionName = null;
    private static $isBodyRendered = false;

    public static function SectionBegin($name) {
        if(self::$sectionName != null) die('Please first use SectionEnd() and then SectionBegin');
        self::BodyEnd();
        self::$sectionName = $name;
        ob_start();
    }
    public static function SectionEnd() {
        if(self::$sectionName == null) die('Please first use SectionBegin(name) and then SectionEnd');
        self::$buffers[self::$sectionName] = ob_get_contents();
        ob_end_clean();
        self::$sectionName = null;
        self::BodyBegin();
    }
    public static function RenderSection($name,$required = true) {
        if(!isset(self::$buffers[$name])) {
            if($required) die('You must use -> SectionBegin("'.$name.'")');
        } else { echo self::$buffers[$name]; unset(self::$buffers[$name]); }
    }
    public static function IsBodyRendered() {return self::$isBodyRendered;}
    public static function BodyBegin() { ob_start(); }
    public static function BodyEnd() { self::$body .= ob_get_contents(); ob_end_clean(); }
    public static function RenderBody() {
        echo self::$body;
        self::$body = null;
        self::$isBodyRendered = true;
    }
}