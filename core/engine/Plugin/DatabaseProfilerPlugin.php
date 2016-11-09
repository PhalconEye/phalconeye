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

namespace Engine\Plugin;

use Engine\Profiler;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Profiler as DatabaseProfiler;
use Phalcon\Events\Event;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * Database profiler plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class DatabaseProfilerPlugin extends PhalconPlugin
{
    /** @var Profiler $_profiler */
    private $_profiler = null;

    /** @var Logger $_logger */
    private $_logger = null;

    /**
     * ViewPlugin constructor.
     */
    public function __construct()
    {
        $config = $this->getDI()->getConfig();
        if ($config->application->profiler && $this->getDI()->has('profiler')) {
            $this->_profiler = new DatabaseProfiler();
            $this->getDI()->getProfiler()->setDbProfiler($this->_profiler);
        }

        if ($config->application->debug) {
            $this->_logger = new File($config->application->logger->path . "db.log");
        }
    }

    /**
     * Before query is executed.
     *
     * @param Event $event Event object.
     *
     * @return bool
     */
    public function beforeQuery($event)
    {
        /** @var Pdo $connection */
        $connection = $event->getSource();
        $statement = $connection->getSQLStatement();
        if ($this->_logger) {
            $this->_logger->log($statement, Logger::INFO);
        }
        if ($this->_profiler) {
            $this->_profiler->startProfile($statement);
        }
    }

    /**
     * After query was executed.
     *
     * @return bool
     */
    public function afterQuery()
    {
        if ($this->_profiler) {
            $this->_profiler->stopProfile();
        }
    }
}