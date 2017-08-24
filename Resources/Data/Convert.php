<?php
namespace Resources\Data;

class Convert {
    public static function IntToDate($int) {
        return intval($int/10000).'/'.(intval($int/100)%100).'/'.($int%100);
    }
    public static function DateToInt($date) {
        $date = explode('/',$date);
        if(count($date) == 3) return $date[0].($date[1] < 10 ? '0'.$date[1] : $date[1]).($date[2] < 10 ? '0'.$date[2] : $date[2]);
        return $date[0];
    }
}
?>