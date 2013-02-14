<?php

class Helper_RenderContent extends \Phalcon\Tag
{
    static public function _($type){
        $content = '';
        $page = Page::findFirst("type = '{$type}'");
        $widgets = $page->getWidgets();
        $widgetRender = new Helper_RenderWidget();

        foreach($widgets as $widget){
            $content .= $widgetRender->_($widget->getWidgetId(), $widget->getParams());
        }

        return $content;
    }
}