<?php
date_default_timezone_set("Asia/Tehran");

if(file_exists(Root.'config.json')) {
    $config = json_decode(file_get_contents(Root.'config.json'));
    foreach($config as $tkey=>$tvalue) {
        $tkey = strtoupper($tkey);
        foreach($tvalue as $key => $value) {
            eval('$value = \''.str_replace('{{',"'.",str_replace('}}',".'",$value)).'\';');
            $key = strtoupper($key);
            define($tkey.'_'.$key,$value);
        }
    }
}
?>