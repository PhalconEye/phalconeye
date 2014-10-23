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
use Phalcon\Exception as PhalconException;

/**
 * Exception class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Exception extends PhalconException
{
    /**
     * Create exception.
     *
     * @param string     $message  Exception message.
     * @param array      $args     Message arguments (for placeholders, sprintf).
     * @param int        $code     Exception code.
     * @param \Exception $previous Previous exception.
     */
    public function __construct($message = "", $args = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct(vsprintf($message, $args), $code, $previous);
    }

    /**
     * Log exception.
     *
     * @param \Exception $e Exception object.
     *
     * @return string
     */
    public static function logException(\Exception $e)
    {
        return self::logError(
            'Exception',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }

    /**
     * Log error.
     *
     * @param string      $type    Type name.
     * @param string      $message Message text.
     * @param string      $file    File path.
     * @param string      $line    Line info.
     * @param string|null $trace   Trace info.
     *
     * @throws Exception
     * @return string
     */
    public static function logError($type, $message, $file, $line, $trace = null)
    {
        $id = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 7);
        $di = DI::getDefault();
        $template = "<%s> [%s] %s (File: %s Line: [%s])";
        $logMessage = sprintf($template, $id, $type, $message, $file, $line);

        if ($di->has('profiler')) {
            $profiler = $di->get('profiler');
            if ($profiler) {
                $profiler->addError($logMessage, $trace);
            }
        }

        if ($trace) {
            $logMessage .= $trace . PHP_EOL;
        } else {
            $logMessage .= PHP_EOL;
        }

        if ($di->has('logger')) {
            $logger = $di->get('logger');
            if ($logger) {
                $logger->error($logMessage);
            } else {
                throw new Exception($logMessage);
            }
        } else {
            throw new Exception($logMessage);
        }

        return $id;
    }
}
