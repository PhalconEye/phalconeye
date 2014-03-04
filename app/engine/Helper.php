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

namespace Engine;

use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Registry;
use Phalcon\Tag;

/**
 * Helper class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class Helper extends Tag
{
    /**
     * Helper constructor is protected.
     *
     * @param DiInterface $di Dependency injection.
     */
    protected function __construct($di)
    {
        $this->setDI($di);
    }

    /**
     * Get helper instance.
     *
     * @param string $name   Helper name.
     * @param string $module Module name.
     *
     * @return mixed
     * @throws Exception
     */
    public static function getInstance($name, $module = 'engine')
    {
        /** @var Registry $registry */
        $di = Di::getDefault();
        $name = ucfirst($name);
        $module = ucfirst($module);
        $fullName = 'Helper\\' . $module . '\\' . $name;

        if (!$di->has($fullName)) {
            /** @var Helper $helperClassName */
            $helperClassName = sprintf('\%s\Helper\%s', $module, $name);
            if (!class_exists($helperClassName)) {
                throw new Exception(
                    sprintf('Can not find Helper with name "%s". Searched in module: %s', $name, $module)
                );
            }

            $helper = new $helperClassName($di);
            $di->set($fullName, $helper, true);
            return $helper;
        }

        return $di->get($fullName);
    }

    /**
     * Call helper through magic method.
     *
     * @param string $name      Helper name.
     * @param array  $arguments Helper arguments.
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        // Collect profile info.
        $di = $this->getDI();
        $profilerIsActive = $di->has('profiler');
        if ($profilerIsActive) {
            $di->get('profiler')->start();
        }

        $result = call_user_func_array(array(&$this, '_' . $name), $arguments);

        // collect profile info
        if ($profilerIsActive) {
            $di->get('profiler')->stop(get_called_class(), 'helper');
        }

        return $result;
    }
}