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
 * @property \\Phalcon\Cache\Backend $cacheData
 */
class Controller extends \Phalcon\Mvc\Controller
{
    /**
     * @var \Phalcon\DiInterface null
     */
    public $di = null;

    /**
     * @var \\Phalcon\Mvc\View null
     */
    public $view = null;

    /**
     * @var string
     */
    private $_widgetName;

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
    public function initialize($widgetName = null, $widgetModule = null, $params = array())
    {
        $this->_widgetName = $widgetName;
        $this->di = \Phalcon\DI::getDefault();
        $this->dispatcher = $this->di->get('dispatcher');
        $this->cacheData = $this->di->get('cacheData');
        $this->_params = $params;

        if ($widgetName !== null) {
            if ($widgetModule !== null) {
                $config = $this->di->get('config');
                $controllerDir = $config->application->modulesDir . $widgetModule . '/Widget/' . $widgetName . '/';
                $defaultModuleName = ucfirst(\Engine\Application::$defaultModule);

                /** @var \Phalcon\Mvc\View $view */
                $this->view = $view = $this->di->get('view');
                $view->setViewsDir($controllerDir);
                $view->setLayoutsDir('../../../' . $defaultModuleName . '/View/layouts/');
                $view->setPartialsDir('../../../' . $defaultModuleName . '/View/partials/');
                $view->setLayout('widget');
            } else {
                $config = $this->di->get('config');
                $controllerDir = $config->application->widgetsDir . $widgetName . '/';
                $defaultModuleName = ucfirst(\Engine\Application::$defaultModule);

                /** @var \Phalcon\Mvc\View $view */
                $this->view = $view = $this->di->get('view');
                $view->setViewsDir($controllerDir);
                $view->setLayoutsDir('../../modules/' . $defaultModuleName . '/View/layouts/');
                $view->setPartialsDir('../../modules/' . $defaultModuleName . '/View/partials/');
                $view->setLayout('widget');
            }
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
    public function getParam($key, $default = null)
    {
        if (!isset($this->_params[$key]))
            return $default;

        return $this->_params[$key];
    }

    /**
     * Get all widget parameters
     *
     * @return array
     */
    public function getAllParams()
    {
        return $this->_params;
    }

    /**
     * Set no render to widget
     *
     * @param bool $flag
     */
    public function setNoRender($flag = true)
    {
        $this->_noRender = $flag;
        return $this;
    }

    /**
     * Get no redner of this widget
     *
     * @return bool
     */
    public function getNoRender()
    {
        return $this->_noRender;
    }

    public function isCached()
    {
        return false;
    }

    public function cacheKey()
    {
        return $this->_widgetName . '_' . $this->_params['content_id'];
    }

    public function cacheLifeTime()
    {
        return 300;
    }

}