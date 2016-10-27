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

namespace Engine\Api;

use Engine\Behavior\DIBehavior;
use Engine\Exception;
use Phalcon\DI;

/**
 * Api container.
 *
 * @category  PhalconEye
 * @package   Engine\Api
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Injector
{
    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    /**
     * Current module name.
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Create api container.
     *
     * @param string $moduleName Module naming.
     * @param DI     $di         Dependency injection.
     */
    public function __construct($moduleName, $di)
    {
        $this->_moduleName = $moduleName;
        $this->__DIConstruct($di);
    }

    /**
     * Get api from container.
     *
     * @param string $name      Api name.
     * @param array  $arguments Api params.
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $apiClassName = sprintf('%s\Api\%sApi', ucfirst($this->_moduleName), ucfirst($name));
        $di = $this->getDI();

        if (!$di->has($apiClassName)) {
            if (!class_exists($apiClassName)) {
                throw new Exception(sprintf('Can not find Api with name "%s".', $name));
            }

            $api = new $apiClassName($this->getDI(), $arguments);
            $di->set($apiClassName, $api, true);
            return $api;
        }

        return $di->get($apiClassName);
    }
}