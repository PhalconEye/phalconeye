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

namespace Engine\Behavior;

use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Dependency container trait.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <vorontsov@phalconeye.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method \Phalcon\Mvc\Model\Transaction\Manager getTransactions()
 * @method \Phalcon\Mvc\Url getUrl()
 * @method \Phalcon\Logger\Adapter getLogger($file = 'main', $format = null)
 * @method \Phalcon\Http\Request getRequest()
 * @method \Phalcon\Http\Response getResponse()
 * @method \Phalcon\Annotations\Adapter getAnnotations()
 * @method \Phalcon\Mvc\Router getRouter()
 * @method \Phalcon\Db\Adapter\Pdo\Mysql getDb()
 * @method \Phalcon\Mvc\Model\Manager getModelsManager()
 * @method \Phalcon\Translate\Adapter getI18n()
 * @method \Phalcon\Events\Manager getEventsManager()
 * @method \Phalcon\Session\Adapter getSession()
 * @method \Phalcon\Registry getRegistry()
 * @method \Phalcon\Cache\BackendInterface getCacheData()
 * @method \Phalcon\Loader getLoader()
 *
 * @method \Engine\Application getApp()
 * @method \Engine\Config getConfig()
 * @method \Engine\Asset\Manager getAssets()
 * @method \Engine\View getView()
 * @method \Engine\Profiler getProfiler()
 * @method \Engine\Package\PackageManager getModules()
 * @method \Engine\Package\PackageManager getWidgets()
 * @method \Engine\Package\PackageManager getPlugins()
 */
trait DIBehavior
{
    /**
     * Dependency injection container.
     *
     * @var DIBehavior|DI
     */
    private $_di;

    /**
     * Create object.
     *
     * @param DiInterface|DIBehavior $di Dependency injection container.
     */
    public function __construct($di = null)
    {
        if ($di == null) {
            $di = DI::getDefault();
        }
        $this->setDI($di);
    }

    /**
     * Set DI.
     *
     * @param DiInterface $di Dependency injection container.
     *
     * @return void
     */
    public function setDI($di)
    {
        $this->_di = $di;
    }

    /**
     * Get DI.
     *
     * @return DIBehavior|DI
     */
    public function getDI()
    {
        return $this->_di;
    }

    /**
     * Proxy to DI.
     *
     * @param string $methodName Method name.
     * @param mixed  $args       Arguments.
     *
     * @return mixed DI method result.
     */
    public function __call($methodName, $args)
    {
        return call_user_func_array(array($this->_di, $methodName), $args);
    }
}