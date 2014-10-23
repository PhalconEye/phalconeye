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

namespace Engine\Console\Command;

use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Console\ConsoleUtil;
use Engine\Db\Schema;
use Phalcon\DI;

/**
 * Database command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
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
                print ConsoleUtil::error('Model with class "' . $model . '" doesn\'t exists.') . PHP_EOL;

                return;
            }
            $count = current($schema->updateTable($model));
            if ($count) {
                print ConsoleUtil::headLine('Table update for model: ' . $model);
                print ConsoleUtil::commandLine('Executed queries:', $count, ConsoleUtil::FG_CYAN);
            } else {
                print ConsoleUtil::success('Table is up to date');
            }
            print PHP_EOL;
        } else {
            $queriesCount = $schema->updateDatabase($cleanup);
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
}