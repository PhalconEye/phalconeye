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

use Engine\DependencyInjection;
use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Widget element.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Element
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
    }

    /**
     * Widget object.
     *
     * @var \stdClass
     */
    protected $_widget;

    /**
     * Widget parameters.
     *
     * @var array
     */
    protected $_widgetParams = [];

    /**
     * Create widget element.
     *
     * @param mixed       $id     Widget id in widgets table.
     * @param array       $params Widgets params in page.
     * @param DiInterface $di     Dependency injection.
     */
    public function __construct($id, $params = [], $di = null)
    {
        $this->__DIConstruct($di);

        // get all widgets metadata and cache it
        $this->_widgetParams = $params;
        $this->_widget = Storage::get($id);
    }

    /**
     * Render widget element.
     *
     * @param string $action Action name.
     *
     * @return mixed
     */
    public function render($action = 'index')
    {
        if (!$this->_widget || !$this->_widget->enabled) {
            return '';
        }

        $widgetName = $this->_widget->name;
        if ($this->_widget->module !== null) {
            $widgetModule = ucfirst($this->_widget->module);
            $controllerClass = "\\{$widgetModule}\\Widget\\{$widgetName}\\Controller";
        } else {
            $widgetModule = null;
            $controllerClass = "\\Widget\\{$widgetName}\\Controller";
        }

        /** @var \Engine\Widget\Controller $controller */
        $controller = new $controllerClass();
        $controller->setDefaults($widgetName, $widgetModule, $this->_widgetParams);

        // check cache
        $output = null;
        $cacheKey = $controller->cacheKey();
        $cacheLifetime = $controller->cacheLifeTime();
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->getDI()->get('cacheOutput');

        if ($controller->isCached()) {
            $output = $cache->get($cacheKey, $cacheLifetime);
        }

        if ($output === null) {
            // collect profiler info
            $config = $this->getDI()->get('config');
            if ($config->application->debug && $this->getDI()->has('profiler')) {
                $this->getDI()->get('profiler')->start();
            }

            $controller->prepare();
            $controller->{"{$action}Action"}();

            // collect profiler info
            if ($config->application->debug && $this->getDI()->has('profiler')) {
                $this->getDI()->get('profiler')->stop($controllerClass, 'widget');
            }

            if ($controller->getNoRender()) {
                return '';
            }
            $output = trim($controller->view->getRender('', 'index'));
            if ($controller->isCached()) {
                $cache->save($cacheKey, $output, $cacheLifetime);
            }
        }

        return $output;
    }
}