<?php

class ViewBag {
    private $data = array();

    public function __get($name) {
        return (isset($this->data[$name]) ? $this->data[$name] : null);
    }

    public function __set($name,$value) {
        $this->data[$name] = $value;
    }
}
?>