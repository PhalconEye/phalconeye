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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  +------------------------------------------------------------------------+
*/
namespace Engine\Widget;

use Engine\Behaviour\DIBehaviour;
use Phalcon\Di;

/**
 * Widget data.
 *
 * @category  PhalconEye\Engine
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class WidgetData
{
    const
        /**
         * Description of widget.
         */
        METADATA_DESCRIPTION = 'description',

        /**
         * Disabled flag.
         */
        METADATA_DISABLED = 'disabled';

    private $_name;
    private $_module;
    private $_metadata;

    /**
     * WidgetData constructor.
     *
     * @param string              $_name      Widget name.
     * @param string|null         $_module    Widget's module.
     * @param DIBehaviour|Di|null $di         Provide DI to collect metadata.
     * @param string              $widgetPath Widget path.
     */
    public function __construct($_name, $_module, $di = null, $widgetPath = null)
    {
        $this->_name = $_name;
        $this->_module = $_module;

        if ($di && !$widgetPath) {
            $this->collectMetadata($this->getWidgetPath($di));
        } elseif ($widgetPath) {
            $this->collectMetadata($widgetPath);
        }
    }

    /**
     * Get widget name.
     *
     * @return string Name.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set widget name.
     *
     * @param string $name Widget name.
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get module name.
     *
     * @return string|null Module name.
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Set module name.
     *
     * @param string|null $module Module name.
     */
    public function setModule($module)
    {
        $this->_module = $module;
    }

    /**
     * Get all or specified metadata.
     *
     * @param string|null $key Provide key to get specific metadata value.
     *
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this->_metadata;
        }

        if (isset($this->_metadata[$key])) {
            return $this->_metadata[$key];
        }

        return null;
    }

    /**
     * Set metadata.
     *
     * @param mixed $metadata Metadata.
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }

    /**
     * Check if widget is disabled.
     *
     * @return bool Disabled?
     */
    public function isDisabled()
    {
        return $this->getMetadata(self::METADATA_DISABLED) === true;
    }

    /**
     * Get widget path.
     *
     * @param DIBehaviour|Di $di Dependency injection.
     *
     * @return string Widget path.
     */
    public function getWidgetPath($di) : string
    {
        $widgetPath = $di->getRegistry()->directories->widgets;
        if ($this->_module != null) {
            $modules = $di->getRegistry()->modules->toArray();
            if (isset($modules[$this->_module])) {
                $widgetPath = $modules[$this->_module] . DS . WidgetCatalog::WIDGET_DIRECTORY . DS;
            }
        }
        $widgetPath .= ucfirst($this->_name);

        return $widgetPath;
    }

    /**
     * Get widget metadata if possible.
     *
     * @param string $widgetPath Widget path.
     *
     * @return void
     */
    public function collectMetadata($widgetPath = null)
    {
        $widgetPath .= DS . WidgetCatalog::WIDGET_METADATA_FILENAME;

        if (file_exists($widgetPath)) {
            $this->setMetadata(include $widgetPath);
        }
    }
}