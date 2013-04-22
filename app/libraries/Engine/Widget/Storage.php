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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine\Widget;


/**
 * Class Storage
 * Used to store all widgets metadata
 * @package Engine
 */
class Storage{
    private static $widgets = array();

    public static function getWidgets(){
        return self::$widgets;
    }

    public static function setWidgets($widgets){
        self::$widgets = $widgets;
    }

    public static function get($widgetId){
        if (empty(self::$widgets[$widgetId]))
            throw new \Exception(sprintf('Widget storage has no widget with id "%s".', $widgetId));


        return self::$widgets[$widgetId];
    }
}