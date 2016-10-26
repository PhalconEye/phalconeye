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

namespace Engine\Console\Command;

use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Utils\ConsoleUtils;
use Engine\Db\Schema;

/**
 * Database command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @CommandName(['database', 'db'])
 * @CommandDescription('Database management.')
 */
class Database extends AbstractCommand implements CommandInterface
{
    /**
     * Update database schema according to models metadata.
     *
     * @param string|null $model   Model name to update. Example: \Test\Model\Class.
     * @param bool        $cleanup Cleanup database? Drop not related tables.
     *
     * @return void
     */
    public function updateAction($model = null, $cleanup = false)
    {
        $schema = new Schema($this->getDI());
        if ($model) {
            if (!class_exists($model)) {
                print ConsoleUtils::error('Model with class "' . $model . '" doesn\'t exists.') . PHP_EOL;

                return;
            }
            $count = current($schema->updateTable($model));
            if ($count) {
                print ConsoleUtils::head('Table update for model: ' . $model);
                print ConsoleUtils::command('Executed queries:', $count, ConsoleUtils::FG_CYAN);
            } else {
                print ConsoleUtils::success('Table is up to date');
            }
            print PHP_EOL;
        } else {
            $queriesCount = $schema->updateDatabase($cleanup);
            if (!empty($queriesCount)) {
                print ConsoleUtils::head('Database update:');
                foreach ($queriesCount as $model => $count) {
                    print ConsoleUtils::command($model . ':', $count, ConsoleUtils::FG_CYAN);
                }
            } else {
                print ConsoleUtils::success('Database is up to date');
            }
            print PHP_EOL;
        }
    }
}