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

use Engine\Console\AbstractCommand;
use Engine\Console\Commands\Assets;
use Engine\Console\Commands\Cache;
use Engine\Console\Commands\Database;
use Engine\Console\CommandsListener;
use Engine\Console\ConsoleUtil;

/**
 * Console class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Cli extends Application
{
    /**
     * Defined engine commands.
     *
     * @var AbstractCommand[]
     */
    private $_commands = [];

    /**
     * Run application.
     *
     * @param string $mode Run mode.
     *
     * @return void
     */
    public function run($mode = 'console')
    {
        parent::run($mode);

        // Init commands.
        $this->_initCommands();
        $this->getEventsManager()->attach('command', new CommandsListener());
    }

    /**
     * Init commands.
     *
     * @return void
     */
    protected function _initCommands()
    {
        $this->_commands[] = new Assets();
        $this->_commands[] = new Database();
        $this->_commands[] = new Cache();
    }

    /**
     * Handle all data and output result.
     *
     * @throws Exception
     * @return mixed
     */
    public function getOutput()
    {
        print ConsoleUtil::infoLine('================================================================', true, 0);
        print ConsoleUtil::infoLine(
            "
           ___  __       __              ____
          / _ \/ / ___ _/ _______  ___  / ____ _____
         / ___/ _ / _ `/ / __/ _ \/ _ \/ _// // / -_)
        /_/  /_//_\_,_/_/\__/\___/_//_/___/\_, /\__/
                                          /___/
                                          Commands Manager", false, 1
        );
        print ConsoleUtil::infoLine('================================================================', false, 2);

        if (!isset($_SERVER['argv'][1])) {
            $this->printAvailableCommands();
            die();
        }

        $input = $_SERVER['argv'][1];

        // Try to dispatch the command
        foreach ($this->_commands as $command) {
            $providedCommands = $command->getCommands();
            if (in_array($input, $providedCommands)) {
                $command->setConfig($this->_config);

                return $this->dispatch($command);
            }
        }

        // Check for alternatives.
        $available = [];
        foreach ($this->_commands as $command) {
            $providedCommands = $command->getCommands();
            foreach ($providedCommands as $command) {
                $soundex = soundex($command);
                if (!isset($available[$soundex])) {
                    $available[$soundex] = [];
                }
                $available[$soundex][] = $command;
            }
        }

        // Show exception with/without alternatives
        $soundex = soundex($input);
        if (isset($available[$soundex])) {
            print ConsoleUtil::warningLine(
                'Command "' . $input . '" not found. Did you mean: ' . join(' or ', $available[$soundex]) . '?'
            );
            $this->printAvailableCommands();
        } else {
            print ConsoleUtil::warningLine('Command "' . $input . '" not found.');
            $this->printAvailableCommands();
        }
    }

    /**
     * Output available commands.
     *
     * @return void
     */
    public function printAvailableCommands()
    {
        print ConsoleUtil::headLine('Available commands:');
        foreach ($this->_commands as $commands) {
            $providedCommands = $commands->getCommands();
            $alias = '';
            if (count($providedCommands) > 1) {
                $alias = 'Aliases: ' . ConsoleUtil::colorize(join(', ', $providedCommands)) . '';
            }
            print ConsoleUtil::commandLine($providedCommands[0], $alias);
        }
        print PHP_EOL;
    }

    /**
     * Dispatch commands.
     *
     * @param AbstractCommand $command Command object.
     *
     * @return bool
     */
    public function dispatch(AbstractCommand $command)
    {
        //If beforeCommand fails abort
        if ($this->_eventsManager->fire('command:beforeCommand', $command) === false) {
            return false;
        }

        //If run the commands fails abort too
        if ($command->run($this->getDI()) === false) {
            return false;
        }

        $this->_eventsManager->fire('command:afterCommand', $command);
    }
}