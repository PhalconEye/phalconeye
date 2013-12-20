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

namespace Engine\Plugin;

use Engine\Application as EngineApplication;
use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Exception as PhalconException;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * Not found plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class NotFound extends PhalconPlugin
{
    /**
     * Before exception is happening.
     *
     * @param Event            $event      Event object.
     * @param Dispatcher       $dispatcher Dispatcher object.
     * @param PhalconException $exception  Exception object.
     *
     * @return bool
     */
    public function beforeException($event, $dispatcher, $exception)
    {
        switch ($exception->getCode()) {
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward(
                    [
                        'module' => EngineApplication::$defaultModule,
                        'namespace' => ucfirst(EngineApplication::$defaultModule) . '\Controller',
                        'controller' => 'Error',
                        'action' => 'show404'
                    ]
                );

                return false;
        }

        return !$event->isStopped();
    }

}