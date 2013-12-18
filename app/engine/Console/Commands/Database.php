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

namespace Engine\Console\Commands;

use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Console\ConsoleUtil;
use Engine\Db\Schema;
use Engine\Generator\Migrations;
use Phalcon\DI;

/**
 * Database command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Database extends AbstractCommand implements CommandInterface
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
        $schema = new Schema($di);

        if ($this->isReceivedOption('model')) {
            $modelClass = $this->getOption('model');
            if (!class_exists($modelClass)) {
                print ConsoleUtil::error('Model with class "' . $modelClass . '" doesn\'t exists.') . PHP_EOL;

                return;
            }
            $count = current($schema->updateTable($modelClass));
            if ($count) {
                print ConsoleUtil::headLine('Table update for model: ' . $modelClass);
                print ConsoleUtil::commandLine('Executed queries:', $count, ConsoleUtil::FG_CYAN);
            } else {
                print ConsoleUtil::success('Table is up to date');
            }
            print PHP_EOL;
        } else {
            $queriesCount = $schema->updateDatabase($this->isReceivedOption('cleanup'));
            if (!empty($queriesCount)) {
                print ConsoleUtil::headLine('Database update:');
                foreach ($queriesCount as $model => $count) {
                    print ConsoleUtil::commandLine($model . ':', $count, ConsoleUtil::FG_CYAN);
                }
            } else {
                print ConsoleUtil::success('Database is up to date');
            }
            print PHP_EOL;
        }
    }

    /**
     * Returns the command identifier.
     *
     * @return string
     */
    public function getCommands()
    {
        return ['database', 'db'];
    }

    /**
     * Prints the help for current command.
     *
     * @return void
     */
    public function getHelp()
    {
        print ConsoleUtil::headLine('Help:');
        print ConsoleUtil::textLine('Database management');

        print ConsoleUtil::commandLine('database update', 'Update database according to models annotations.');
        print PHP_EOL;

        $this->printParameters($this->getPossibleParams());
        print PHP_EOL;
    }

    /**
     * Get possible parameters.
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'model=s' => "Model to update. Default: all.",
            'cleanup' => "Drop not related tables."
        ];
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
}