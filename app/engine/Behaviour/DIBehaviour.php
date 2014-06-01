<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

namespace Engine\Behaviour;

use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Dependency container trait.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method \Phalcon\Mvc\Model\Transaction\Manager getTransactions()
 * @method \Engine\Asset\Manager getAssets()
 * @method \Phalcon\Mvc\Url getUrl()
 * @method \Phalcon\Logger\Adapter getLogger($file = 'main', $format = null)
 * @method \Phalcon\Http\Request getRequest()
 * @method \Phalcon\Http\Response getResponse()
 * @method \Phalcon\Annotations\Adapter getAnnotations()
 * @method \Phalcon\Mvc\Router getRouter()
 * @method \Phalcon\Mvc\View getView()
 * @method \Phalcon\Db\Adapter\Pdo\Mysql getDb()
 * @method \Phalcon\Mvc\Model\Manager getModelsManager()
 * @method \Phalcon\Config getConfig()
 * @method \Phalcon\Translate\Adapter getI18n()
 * @method \Phalcon\Events\Manager getEventsManager()
 * @method \Phalcon\Session\Adapter getSession()
 */
trait DIBehaviour
{
    /**
     * Dependency injection container.
     *
     * @var DIBehaviour|DI
     */
    private $_di;

    /**
     * Create object.
     *
     * @param DiInterface|DIBehaviour $di Dependency injection container.
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
     * @return DIBehaviour|DI
     */
    public function getDI()
    {
        return $this->_di;
    }
}