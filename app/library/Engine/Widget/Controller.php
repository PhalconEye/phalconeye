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
 * @property \Phalcon\Cache\Backend $cacheData
 */
class Widget_Controller extends \Phalcon\Mvc\Controller
{
    /**
     * @var Phalcon\DiInterface null
     */
    public $di = null;

    /**
     * @var \Phalcon\Mvc\View null
     */
    public $view = null;

    /**
     * @var array Widget parameters
     */
    private $_params = array();

    /**
     * @var bool Defines if output exists
     */
    private $_noRender = false;

    /**
     * Initializes the controller
     */
    public function initialize($widgetName = null, $params = array())
    {
        $this->di = Phalcon\DI::getDefault();
        $this->dispatcher = $this->di->get('dispatcher');
        $this->cacheData = $this->di->get('cacheData');
        $this->_params = $params;

        if ($widgetName !== null) {
            $config = $this->di->get('config');
            $controllerDir = $config->application->miscDir . "Widget/{$widgetName}/";

            $this->view = $view = $this->di->get('view');
            $view->setViewsDir($controllerDir);
        }

        // run init function
        if (method_exists($this, 'init'))
            $this->init();
    }

    /**
     * Get widget parameter
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null){
        if (!isset($this->_params[$key]))
            return $default;

        return $this->_params[$key];
    }

    /**
     * Get all widget parameters
     *
     * @return array
     */
    public function getAllParams(){
        return $this->_params;
    }

    /**
     * Set no render to widget
     *
     * @param bool $flag
     */
    public function setNoRender($flag = true){
        $this->_noRender = $flag;
    }

    /**
     * Get no redner of this widget
     *
     * @return bool
     */
    public function getNoRender(){
        return $this->_noRender;
    }

}