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

use Engine\Console\CommandsListener,
    Engine\Console\ConsoleUtil,
    Engine\Console\Command as PeCommand;


class Cli extends Application
{
    /**
     * Defined engine commands.
     *
     * @var PeCommand[]
     */
    private $_commands = array();

    /**
     * Run application.
     */
    public function run($mode = 'console')
    {
        parent::run($mode);

        // Init commands.
        $this->_initCommands();
        $this->getEventsManager()->attach('command', new CommandsListener());
    }

    protected function _initCommands()
    {
        $this->_commands[] = new \Engine\Console\Commands\Assets();
        $this->_commands[] = new \Engine\Console\Commands\Database();
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
        print ConsoleUtil::infoLine("
           ___  __       __              ____
          / _ \/ / ___ _/ _______  ___  / ____ _____
         / ___/ _ / _ `/ / __/ _ \/ _ \/ _// // / -_)
        /_/  /_//_\_,_/_/\__/\___/_//_/___/\_, /\__/
                                          /___/
                                          Commands Manager", false, 1);
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
        $available = array();
        foreach ($this->_commands as $command) {
            $providedCommands = $command->getCommands();
            foreach ($providedCommands as $command) {
                $soundex = soundex($command);
                if (!isset($available[$soundex])) {
                    $available[$soundex] = array();
                }
                $available[$soundex][] = $command;
            }
        }

        // Show exception with/without alternatives
        $soundex = soundex($input);
        if (isset($available[$soundex])) {
            print ConsoleUtil::warningLine('Command "' . $input . '" not found. Did you mean: ' . join(' or ', $available[$soundex]) . '?');
            $this->printAvailableCommands();
        } else {
            print ConsoleUtil::warningLine('Command "' . $input . '" not found.');
            $this->printAvailableCommands();
        }
    }

    /**
     * Output available commands.
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
     * @param PeCommand $command
     *
     * @return bool
     */
    public function dispatch(PeCommand $command)
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