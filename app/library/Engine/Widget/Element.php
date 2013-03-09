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

/**
 * Provides rendering for widget
 */
class Widget_Element
{

    /**
     * @var Phalcon\DiInterface null
     */
    protected $_di = null;

    /**
     * @var Widget null
     */
    protected $_widget = null;

    /**
     * @var array Widget parameters
     */
    protected $_widgetParams = array();

    /**
     * @param $id - widget id in database
     * @param $params - widgets params in page
     */
    public function __construct($id, $params = array()){

        // get all widgets metadata and cache it
        $this->_widgetParams = $params;
        $this->_di = $di = Phalcon\DI::getDefault();
        $cache = $di->get('cacheData');
        $cacheKey = "widgets_metadata.cache";
        $widgets = $cache->get($cacheKey);

        if ($widgets === null){
            $widgetObjects = Widget::find();
            $widgets = array();
            foreach($widgetObjects as $object){
                $widgets[$object->getId()] = $object;
            }

            $cache->save($cacheKey, $widgets); // 1 day
        }

        if (!empty($widgets[$id]))
            $this->_widget = $widgets[$id];

    }

    public function render($action = 'index'){
        if (!$this->_widget || !$this->isAllowed()){
            return '';
        }


        $widgetName = $this->_widget->getName();
        $controllerClass = "Widget_{$widgetName}_Controller";

        /** @var Widget_Controller $controller  */
        $controller = new $controllerClass();
        $controller->initialize($widgetName, $this->_widgetParams);
        $controller->{"{$action}Action"}();

        if ($controller->getNoRender())
           return '';

        return $controller->view->getRender('', 'index');
    }

    private function isAllowed(){
        $viewer = User::getViewer();
        if (empty($this->_widgetParams['roles']) || !is_array($this->_widgetParams['roles'])) return true;
        return in_array($viewer->getRoleId(), $this->_widgetParams['roles']);
    }
}