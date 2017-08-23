<?php
namespace Resources\Http;

abstract class Response {
    public static function Redirect($url) {
        ob_end_clean(); 
        header('Location: '.$url); 
        exit();
    }
}