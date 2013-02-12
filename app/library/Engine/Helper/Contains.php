<?php

class Helper_Contains extends \Phalcon\Tag
{
    static public function _($what, $where){
        return in_array($what, $where);
    }
}