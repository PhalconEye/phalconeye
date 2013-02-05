<?php

class Helper_RenderWidget extends \Phalcon\Tag
{
    static public function _($id, $params){
        $widget = new Widget_Element($id, $params);
        return $widget->render();
    }
}