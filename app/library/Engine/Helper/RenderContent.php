<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

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