<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\Widget;

use \Engine\Exception as EngineException;

/**
 * Widget storage.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Storage
{
    /**
     * Widgets in storage.
     *
     * @var array
     */
    private static $_widgets = [];

    /**
     * Get widgets in storage.
     *
     * @return array
     */
    public static function getWidgets()
    {
        return self::$_widgets;
    }

    /**
     * Insert widgets to storage.
     *
     * @param array $widgets Widgets.
     */
    public static function setWidgets($widgets)
    {
        self::$_widgets = $widgets;
    }

    /**
     * Get widget by ID.
     *
     * @param mixed $widgetId Widget identity in storage.
     *
     * @return mixed
     * @throws \Engine\Exception
     */
    public static function get($widgetId)
    {
        if (empty(self::$_widgets[$widgetId])) {
            throw new EngineException(sprintf('Widget storage has no widget with id "%s".', $widgetId));
        }

        return self::$_widgets[$widgetId];
    }
}