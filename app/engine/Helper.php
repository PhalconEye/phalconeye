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
 *
 * @TODO: try to change this to \Phalcon\Registry and DI...
 */
abstract class Helper extends Tag
{
    /**
     * Helpers cache.
     *
     * @var array
     */
    protected static $_cache;

    /**
     * Helper constructor is protected.
     */
    protected function __construct()
    {
        $this->setDI(Di::getDefault());
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
        $name = ucfirst($name);
        $module = ucfirst($module);

        if (!isset(self::$_cache[$module . '_' . $name])) {
            /** @var Helper $helperClassName */
            $helperClassName = sprintf('\%s\Helper\%s', $module, $name);
            if (!class_exists($helperClassName)) {
                throw new Exception(
                    sprintf('Can not find Helper with name "%s". Searched in module: %s', $name, $module)
                );
            }

            self::$_cache[$module . '_' . $name] = new $helperClassName();
        }

        return self::$_cache[$module . '_' . $name];
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
        $config = $di->get('config');
        $profilerIsActive = $config->application->debug && $di->has('profiler');
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