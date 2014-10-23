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

namespace Engine\Api;

use Engine\Behaviour\DIBehaviour;
use Engine\Exception;
use Phalcon\DI;

/**
 * Api container.
 *
 * @category  PhalconEye
 * @package   Engine\Api
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Injector
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
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
        $apiClassName = sprintf('%s\Api\%s', ucfirst($this->_moduleName), ucfirst($name));
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