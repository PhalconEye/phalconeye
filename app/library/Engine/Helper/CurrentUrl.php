<?php

class Helper_CurrentUrl extends \Phalcon\Tag
{
    static public function _(){
        return Phalcon\DI::getDefault()->get('request')->get('_url');
    }
}