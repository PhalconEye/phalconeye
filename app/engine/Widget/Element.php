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
 * Provides rendering for widget
 */
class Element
{

    /**
     * @var \Phalcon\DiInterface null
     */
    protected $_di = null;

    /**
     * @var \stdClass null
     */
    protected $_widget = null;

    /**
     * @var array Widget parameters
     */
    protected $_widgetParams = array();

    /**
     * @param $id - widget id in widgets table
     * @param $params - widgets params in page
     */
    public function __construct($id, $params = array())
    {

        // get all widgets metadata and cache it
        $this->_widgetParams = $params;
        $this->_di = $di = \Phalcon\DI::getDefault();
        $this->_widget = Storage::get($id);

    }

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
        $cache = $this->_di->get('cacheOutput');

        if ($controller->isCached()) {
            $output = $cache->get($cacheKey, $cacheLifetime);
        }

        if ($output === null) {
            // collect profiler info
            $config = $this->_di->get('config');
            if ($config->application->debug && $this->_di->has('profiler')){
                $this->_di->get('profiler')->start();
            }

            $controller->start();
            $controller->{"{$action}Action"}();

            // collect profiler info
            if ($config->application->debug && $this->_di->has('profiler')){
                $this->_di->get('profiler')->stop($controllerClass, 'widget');
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