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

namespace Engine\Helper;

use Engine\Behavior\DIBehavior;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Tag;

/**
 * Helper class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method static DIBehavior|Di getDi()
 */
abstract class AbstractHelper extends Tag
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
     * @param DiInterface|string $nameOrDI Helper name.
     * @param string             $module   Module name.
     *
     * @return $this
     * @throws Exception
     */
    public static function getInstance($nameOrDI, $module = 'engine')
    {
        if ($nameOrDI instanceof DiInterface) {
            $di = $nameOrDI;
            $helperClassName = get_called_class();
        } else {
            $di = DI::getDefault();
            $nameOrDI = ucfirst($nameOrDI);
            $module = ucfirst($module);
            $helperClassName = sprintf('%s\Helper\%sHelper', $module, $nameOrDI);
        }

        if (!$di->has($helperClassName)) {
            /** @var AbstractHelper $helperClassName */
            if (!class_exists($helperClassName)) {
                throw new \Exception(
                    sprintf('Can not find Helper with name "%s". Searched in module: %s', $nameOrDI, $module)
                );
            }

            $helper = new $helperClassName($di);
            $di->set($helperClassName, $helper, true);
            return $helper;
        }

        return $di->get($helperClassName);
    }
}