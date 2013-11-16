<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2013 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Engine\Console\Commands;

use Engine\Console\Color,
    Engine\Console\Command,
    Engine\Console\CommandsInterface,
    Engine\Generator\Migrations;

use Phalcon\DI;

/**
 * Migration
 *
 * Generates/Run a migration
 *
 * @category    Phalcon
 * @package     Command
 * @subpackage  Controller
 * @copyright   Copyright (c) 2011-2013 Phalcon Team (team@phalconphp.com)
 * @license     New BSD License
 */
class Migration extends Command implements CommandsInterface
{
    /**
     * Executes the command.
     *
     * @param DI $di Dependency injection.
     *
     * @return void|bool
     */
    public function run($di)
    {
        if ($this->isReceivedOption('table')) {
            $tableName = $this->getOption('table');
        } else {
            $tableName = 'all';
        }

        $migrationsDir = ROOT_PATH . '/app/migrations';

        $isForModule = $this->isReceivedOption('module');
        $moduleName = null;
        if ($isForModule) {
            $moduleName = $this->getOption('module');
            $migrationsDir = $this->getConfig()->application->modulesDir . ucfirst($moduleName) . '/Migrations';
        }
        $forcedVersion = $this->getOption('version');

        $action = $this->getOption(array('action', 1));
        $subAction = $this->getOption(array('action', 2));

        if ($action == 'generate') {
            $generatorOptions = array(
                'config' => $this->getConfig(),
                'tableName' => $tableName,
                'migrationsDir' => $migrationsDir,
                'originalVersion' => $forcedVersion,
                'isForModule' => $isForModule,
                'moduleName' => $moduleName,
                'force' => $this->isReceivedOption('force')
            );

            try {
                if ($subAction == 'empty') {
                    $version = Migrations::generateEmpty($generatorOptions);
                } else {
                    $version = Migrations::generate($generatorOptions);
                }
                print Color::success('Version ' . $version . ' was successfully generated') . PHP_EOL;
            } catch (Migrations\Exception\MigrationExists $e) {
                print Color::error($e->getMessage()) . PHP_EOL;
            }

        } else {
            if ($action == 'run') {
                $version = Migrations::run(array(
                    'config' => $this->getConfig(),
                    'migrationsDir' => $migrationsDir,
                    'toVersion' => $forcedVersion,
                    'force' => $this->isReceivedOption('force')
                ));

                print Color::success('Version ' . $version . ' was successfully migrated') . PHP_EOL;
            }
        }

    }

    /**
     * Returns the command identifier
     *
     * @return string
     */
    public function getCommands()
    {
        return array('migration', 'mig');
    }

    /**
     * Prints the help for current command.
     *
     * @return void
     */
    public function getHelp()
    {
        print Color::headLine('Help:');
        print Color::textLine('Generates/Run a Migration');

        print Color::headLine('Usage: Generate a Migration');
        print Color::commandLine('migration generate');
        print Color::commandLine('migration generate empty');
        print PHP_EOL;

        print Color::headLine('Usage: Run a Migration');
        print Color::commandLine('migration run');
        print PHP_EOL;

        $this->printParameters($this->getPossibleParams());
        print PHP_EOL;
    }

    /**
     * Returns number of required parameters for this command.
     *
     * @return int
     */
    public function getRequiredParams()
    {
        return 1;
    }

    /**
     * Get possible parameters.
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return array(
            'table=s' => "Table to migrate. Default: all.",
            'version=s' => "Version to migrate.",
            'module=s' => "Select module to use migration.",
            'force' => "Forces to overwrite existing migrations.",
        );
    }
}