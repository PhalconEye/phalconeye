<?php

class Helper_Setting extends \Phalcon\Tag
{
    static public function _($name, $default = null){
        return Settings::getSetting($name, $default);
    }
}