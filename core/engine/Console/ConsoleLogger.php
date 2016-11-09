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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  +------------------------------------------------------------------------+
*/

namespace Engine\Console;

use Engine\Logger;
use Engine\Utils\ConsoleUtils;

/**
 * Console logger. Wrapper for default logger.
 *
 * @category  PhalconEye
 * @package   Engine\Console
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ConsoleLogger extends Logger
{
    private $_logger;

    /**
     * ConsoleLogger constructor.
     *
     * @param Logger $logger Parent logger.
     */
    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($type, $message = null, array $context = null)
    {
        $this->_logger->log($type, $message, $context);
        $this->_log($type, $message);
    }

    /**
     * Log to console.
     *
     * @param int    $type    Message type.
     * @param string $message Message to log.
     */
    private function _log($type, $message)
    {
        print ConsoleUtils::log($type, $message) . PHP_EOL;
    }

    /**
     * Proxy to original logger.
     *
     * @param string $methodName Method name.
     * @param mixed  $args       Arguments.
     *
     * @return mixed DI method result.
     */
    public function __call($methodName, $args)
    {
        if ($methodName == 'log') {
            return call_user_func_array(array($this, $methodName), $args);
        }

        return call_user_func_array(array($this->_logger, $methodName), $args);
    }
}