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

namespace Engine;

use Phalcon\Mvc\Dispatcher as PhalconDispatcher;

/**
 * Application dispatcher.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Dispatcher extends PhalconDispatcher
{
    /**
     * Gets last dispatched controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        $parts = explode('_', parent::getControllerName());
        $finalHandlerName = '';

        foreach ($parts as $part) {
            $finalHandlerName .= ucfirst($part);
        }
        return $finalHandlerName;
    }

}