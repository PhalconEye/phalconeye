<?php

class Helper_Translate extends \Phalcon\Tag
{
      static public function _($index, $placeholders=null){
         return Phalcon\DI::getDefault()->get('trans')->query($index, $placeholders);
      }
}