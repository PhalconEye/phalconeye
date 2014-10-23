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

use Phalcon\Mvc\Dispatcher as PhalconDispatcher;

/**
 * Application dispatcher.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Dispatcher extends PhalconDispatcher
{
    /**
     * Dispatch.
     * Override it to use own logic.
     *
     * @throws \Exception
     * @return object
     */
    public function dispatch()
    {
        try {
            $parts = explode('_', $this->_handlerName);
            $finalHandlerName = '';

            foreach ($parts as $part) {
                $finalHandlerName .= ucfirst($part);
            }
            $this->_handlerName = $finalHandlerName;
            $this->_actionName = strtolower($this->_actionName);

            return parent::dispatch();
        } catch (\Exception $e) {
            $this->_handleException($e);

            if (APPLICATION_STAGE == APPLICATION_STAGE_DEVELOPMENT) {
                throw $e;
            } else {
                $id = Exception::logError(
                    'Exception',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );

                $this->getDI()->setShared(
                    'currentErrorCode',
                    function () use ($id) {
                        return $id;
                    }
                );
            }
        }

        return parent::dispatch();
    }
}