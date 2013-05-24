<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

use \Phalcon\DI\FactoryDefault as Di;
use \Phalcon\Exception as PhException;

class Error
{
    /**
     * Log normal error
     *
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     */
    public static function normal($type, $message, $file, $line)
    {
        // Log it
        self::logError(
            $type,
            $message,
            $file,
            $line
        );

        // Display it under regular circumstances
    }

    /**
     * Trigger error_get_last
     */
    public static function shutdown()
    {
        $error = error_get_last();
        if (!$error)
            return;

        // Log it
        self::logError(
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );

        // Display it under regular circumstances
    }

    /**
     * Log exception
     *
     * @param \Exception $exception
     */
    public static function exception($exception)
    {
        // Log the error
        self::logError(
            'Exception',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        // Display it
    }

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
                throw new PhException($logMessage);
            }
        } else {
            throw new PhException($logMessage);
        }
    }
}