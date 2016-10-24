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

use Engine\Application;
use Engine\Behaviour\DIBehaviour;
use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Widget element.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Element
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
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
        $this->_widget = $di->get('widgets')->get($id);
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

        // Check cache.
        $output = null;
        $cacheKey = $controller->getCacheKey();
        $cacheLifeTime = $controller->getCacheLifeTime();
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->getDI()->get('cacheOutput');

        if ($controller->isCached()) {
            $output = $cache->get($cacheKey, $cacheLifeTime);
        }

        if ($output === null) {
            // Collect profiler info.
            $hasProfiler = $this->getDI()->has('profiler');
            if ($hasProfiler) {
                $this->getDI()->getProfiler()->start();
            }

            $controller->prepare($action);
            $controller->{"{$action}Action"}();

            // collect profiler info
            if ($hasProfiler) {
                $this->getDI()->getProfiler()->stop($controllerClass, 'widget');
            }

            if ($controller->getNoRender()) {
                return '';
            }

            $controller->view->getRender(null, null);
            $output = $controller->view->getContent();

            if ($controller->isCached()) {
                $cache->save($cacheKey, trim($output), $cacheLifeTime);
            }
        }

        // install assets.
        $assets = $this->getDi()->getAssets();
        $css = $controller->getCssAssets();
        $js = $controller->getJsAssets();
        if (!empty($css)) {
            $cssCollection = $assets->get('css');
            foreach ($css as $value) {
                $cssCollection->addCss($value);
            }
        }

        if (!empty($js)) {
            $jsCollection = $assets->get('js');
            foreach ($js as $value) {
                $jsCollection->addJs($value);
            }
        }

        return $output;
    }
}
