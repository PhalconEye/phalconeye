<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\Widget;

use Engine\Exception as EngineException;

/**
 * Widgets catalog.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class WidgetCatalog
{
    const
        /**
         * Widget namespace when it is outside of any module.
         */
        WIDGET_EXTERNAL_NAMESPACE = '_external_',

        /**
         * Widget directory name.
         */
        WIDGET_DIRECTORY = 'Widget',

        /**
         * Widget metadata filename.
         */
        WIDGET_METADATA_FILENAME = 'metadata.php';

    /**
     * Widgets in catalog.
     *
     * @var array
     */
    protected $_widgets = [];

    /**
     * Lookup widgets in module.
     *
     * @param string $module     Module name.
     * @param string $modulePath Module path.
     *
     * @return array List of widgets.
     */
    public static function getWidgetsFromModule($module, $modulePath) : array
    {
        $widgets = [];
        $modulePath .= ucfirst($module) . DS . self::WIDGET_DIRECTORY;

        if (!file_exists($modulePath)) {
            return [];
        }

        foreach (new \DirectoryIterator($modulePath) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $widgets[] = new WidgetData($file->getBasename(), $module, null, $file->getPath());
            }
        }

        return $widgets;
    }

    /**
     * Add one widget to catalog.
     *
     * @param WidgetData $widget Widget model.
     *
     * @return void
     * @throws EngineException
     */
    public function add(WidgetData $widget)
    {
        $key = $this->_getKey($widget->getName(), $widget->getModule());
        if (isset($this->_widgets[$key])) {
            throw new EngineException(sprintf('Widget catalog has already widget with id "%s".', $key));
        }

        $this->_widgets[$key] = $widget;
    }

    /**
     * Add all widgets to catalog.
     *
     * @param array $widgets Widgets.
     *
     * @return void
     * @throws EngineException
     */
    public function addAll($widgets)
    {
        foreach ($widgets as $widget) {
            $this->add($widget);
        }
    }

    /**
     * Get widget from catalog.
     *
     * @param string $key Widget key.
     *
     * @return WidgetData
     * @throws EngineException
     */
    public function get($key) : WidgetData
    {
        if (!isset($this->_widgets[$key])) {
            throw new EngineException(sprintf('Widget catalog has no widget with id "%s".', $key));
        }

        return $this->_widgets[$key];
    }

    /**
     * Get widgets in catalog.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_widgets;
    }

    /**
     * Get unique widget identifier.
     *
     * @param string      $name   Widget name.
     * @param string|null $module Widget's module.
     *
     * @return string
     */
    protected function _getKey($name, $module = null) : string
    {
        if (!$module) {
            $module = self::WIDGET_EXTERNAL_NAMESPACE;
        }

        return $module . '|' . $name;
    }
}