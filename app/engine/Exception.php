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

namespace Engine;

use Phalcon\Exception as PhalconException;

/**
 * Exception class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Exception extends PhalconException
{
    /**
     * Inject logging logic.
     *
     * @param string    $message  Message text.
     * @param int       $code     Exception code.
     * @param Exception $previous Previous exception.
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        self::exception($this);
    }

    /**
     * Log normal error.
     *
     * @param string $type    Exception type.
     * @param string $message Message text.
     * @param string $file    File path.
     * @param string $line    Line info.
     *
     * @return void
     */
    public static function normal($type, $message, $file, $line)
    {
        self::logError(
            $type,
            $message,
            $file,
            $line
        );
    }

    /**
     * Shutdown handler.
     *
     * @return void
     */
    public static function shutdown()
    {
        $error = error_get_last();
        if (!$error) {
            return;
        }

        self::logError(
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );
    }

    /**
     * Log exception.
     *
     * @param Exception $exception Exception object.
     *
     * @return void
     */
    public static function exception($exception)
    {
        self::logError(
            'Exception',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }

    /**
     * Log error.
     *
     * @param string $type    Type name.
     * @param string $message Message text.
     * @param string $file    File path.
     * @param string $line    Line info.
     * @param string $trace   Trace info.
     *
     * @throws PhalconException
     */
    protected static function logError($type, $message, $file, $line, $trace = '')
    {
        $di = Di::getDefault();
        $template = "[%s] %s (File: %s Line: [%s])";
        $logMessage = sprintf($template, $type, $message, $file, $line);

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
                throw new PhalconException($logMessage);
            }
        } else {
            throw new PhalconException($logMessage);
        }
    }
}
