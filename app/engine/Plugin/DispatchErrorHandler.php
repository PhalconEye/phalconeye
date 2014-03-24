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

namespace Engine\Plugin;

use Engine\Application as EngineApplication;
use Engine\Exception as EngineException;
use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Exception as PhalconException;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * Not found plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class DispatchErrorHandler extends PhalconPlugin
{
    /**
     * Before exception is happening.
     *
     * @param Event            $event      Event object.
     * @param Dispatcher       $dispatcher Dispatcher object.
     * @param PhalconException $exception  Exception object.
     *
     * @throws \Phalcon\Exception
     * @return bool
     */
    public function beforeException($event, $dispatcher, $exception)
    {
        // Handle 404 exceptions.
        if ($exception instanceof DispatchException) {
            $dispatcher->forward(
                [
                    'module' => EngineApplication::SYSTEM_DEFAULT_MODULE,
                    'namespace' => ucfirst(EngineApplication::SYSTEM_DEFAULT_MODULE) . '\Controller',
                    'controller' => 'Error',
                    'action' => 'show404'
                ]
            );

            return false;
        }

        if (APPLICATION_STAGE == APPLICATION_STAGE_DEVELOPMENT) {
            throw $exception;
        } else {
            EngineException::logException($exception);
        }

        // Handle other exceptions.
        $dispatcher->forward(
            [
                'module' => EngineApplication::SYSTEM_DEFAULT_MODULE,
                'namespace' => ucfirst(EngineApplication::SYSTEM_DEFAULT_MODULE) . '\Controller',
                'controller' => 'Error',
                'action' => 'show500'
            ]
        );

        return $event->isStopped();
    }
}