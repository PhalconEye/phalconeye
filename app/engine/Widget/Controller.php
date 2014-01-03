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

use Engine\Application;
use Phalcon\DI;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View;

/**
 * Widget controller.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @property \Phalcon\Cache\Backend $cacheData
 */
class Controller extends PhalconController
{
    /**
     * Dependency injection.
     *
     * @var DI
     */
    public $di = null;

    /**
     * View object.
     *
     * @var View
     */
    public $view = null;

    /**
     * Widget name.
     *
     * @var string
     */
    private $_widgetName;

    /**
     * Widget module.
     *
     * @var string
     */
    private $_widgetModule;

    /**
     * Widget params.
     *
     * @var array
     */
    private $_params = [];

    /**
     * Defines if output exists.
     *
     * @var bool
     */
    private $_noRender = false;

    /**
     * Set widget default data aka __construct().
     *
     * @param string      $widgetName   Widget naming.
     * @param string|null $widgetModule Widget module name.
     * @param array       $params       Widget params.
     */
    public function setDefaults($widgetName, $widgetModule = null, $params = [])
    {
        $this->_widgetName = $widgetName;
        $this->_widgetModule = $widgetModule;
        $this->_params = $params;
    }

    /**
     * Prepare controller.
     *
     * @return void
     */
    public function prepare()
    {
        $this->di = DI::getDefault();
        $this->dispatcher = $this->di->get('dispatcher');
        $this->cacheData = $this->di->get('cacheData');

        if ($this->_widgetName !== null) {
            if ($this->_widgetModule !== null) {
                $config = $this->di->get('config');
                $controllerDir = $config->application->modulesDir .
                    $this->_widgetModule .
                    '/Widget/' .
                    $this->_widgetName . '/';
                $defaultModuleName = ucfirst(Application::SYSTEM_DEFAULT_MODULE);

                /** @var \Phalcon\Mvc\View $view */
                $this->view = $view = $this->di->get('view');
                $view->setViewsDir($controllerDir);
                $view->setLayoutsDir('../../../' . $defaultModuleName . '/View/layouts/');
                $view->setPartialsDir('../../../' . $defaultModuleName . '/View/partials/');
                $view->setLayout('widget');
            } else {
                $config = $this->di->get('config');
                $controllerDir = $config->application->widgetsDir . $this->_widgetName . '/';
                $defaultModuleName = ucfirst(Application::SYSTEM_DEFAULT_MODULE);

                /** @var \Phalcon\Mvc\View $view */
                $this->view = $view = $this->di->get('view');
                $view->setViewsDir($controllerDir);
                $view->setLayoutsDir('../../modules/' . $defaultModuleName . '/View/layouts/');
                $view->setPartialsDir('../../modules/' . $defaultModuleName . '/View/partials/');
                $view->setLayout('widget');
            }
        }

        // run init function
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * Get widget parameter.
     *
     * @param string $key     Param name.
     * @param null   $default Param default value.
     *
     * @return null
     */
    public function getParam($key, $default = null)
    {
        if (!isset($this->_params[$key])) {
            return $default;
        }

        return $this->_params[$key];
    }

    /**
     * Get all widget parameters.
     *
     * @return array
     */
    public function getAllParams()
    {
        return $this->_params;
    }

    /**
     * Set no render for widget.
     *
     * @param bool $flag Disable render?
     *
     * @return $this
     */
    public function setNoRender($flag = true)
    {
        $this->_noRender = $flag;

        return $this;
    }

    /**
     * Get no render of this widget.
     *
     * @return bool
     */
    public function getNoRender()
    {
        return $this->_noRender;
    }

    /**
     * Widget can be cached?
     *
     * @return bool
     */
    public function isCached()
    {
        return false;
    }

    /**
     * Get widget cache key.
     *
     * @return string
     */
    public function cacheKey()
    {
        return $this->_widgetName . '_' . $this->_params['content_id'];
    }

    /**
     * Get widget cache lifetime.
     *
     * @return int
     */
    public function cacheLifeTime()
    {
        return 300;
    }
}