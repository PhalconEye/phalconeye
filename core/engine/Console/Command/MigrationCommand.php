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
use Engine\Migration\MigrationData;
use Engine\Migration\MigrationManager;
use Engine\Utils\ConsoleUtils;

/**
 * Migration command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @CommandName(['migration', 'mg'])
 * @CommandDescription('Migrations management.')
 */
class MigrationCommand extends AbstractCommand implements CommandInterface
{
    /**
     * Migrate to new version.
     *
     * @return void
     */
    public function migrateAction()
    {

    }

    /**
     * Migrate to new version.
     *
     * @param string $module Show status for that module.
     *
     * @return void
     */
    public function statusAction($module = null)
    {
        $migrationManager = new MigrationManager($this->getDI());

        if (empty($module)) {
            $result = $migrationManager->getStatus();
            print ConsoleUtils::head("Status by modules: ") . PHP_EOL;

            foreach ($result as $module => $count) {
                print ConsoleUtils::text($module . ':');
                print ConsoleUtils::tab(30, 150);
                if ($count > 0) {
                    print ConsoleUtils::warn($count) . PHP_EOL;
                } else {
                    print ConsoleUtils::info('up to date', false, 0) . PHP_EOL;
                }
            }
        } else {
            if (!$this->getDI()->getModules()->has($module)) {
                print ConsoleUtils::errorLine("Module '$module' doesn't exist!") . PHP_EOL;
                return;
            }

            $moduleData = $this->getDI()->getModules()->get($module);
            $result = $migrationManager->getMigrationsToMigrate($moduleData);
            if (empty($result)) {
                print ConsoleUtils::successLine("Module '$module' is up to date") . PHP_EOL;
                return;
            }

            print ConsoleUtils::head("Need to be migrated in '$module': ") . PHP_EOL;
            /** @var MigrationData $item */
            foreach ($result as $item) {
                print ConsoleUtils::text(" - {$item->getVersion()}") . PHP_EOL;
            }
            print PHP_EOL;
        }
    }

    /**
     * Create new migration.
     *
     * @param string $module Module name in which migration must be created.
     *
     * @return void
     */
    public function createAction($module)
    {
        $migrationManager = new MigrationManager($this->getDI());
        $migration = $migrationManager->create(strtolower($module));
        print ConsoleUtils::successLine($migration) . PHP_EOL;
    }
}