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

namespace Engine;

use Phalcon\Logger\Adapter;

/**
 * Logger wrapper.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Logger extends Adapter\File
{
    /**
     * Log exception with message.
     *
     * @param \Exception  $exception Exception object.
     * @param string|null $message   Message text. If empty - message from exception will be taken.
     */
    public function exception(\Exception $exception, $message = null)
    {
        if (empty($message)) {
            $message = $exception->getMessage();
        }

        parent::error($message . ': ' . PHP_EOL . $exception->getTraceAsString());
    }
}