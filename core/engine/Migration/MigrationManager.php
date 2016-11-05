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

namespace Engine\Migration;

use Engine\Behavior\DIBehavior;
use Engine\Migration\Model\MigrationModel;
use Engine\Package\PackageData;
use Engine\Package\PackageGenerator;
use Engine\Utils\ConsoleUtils;
use Engine\Utils\FileUtils;
use Phalcon\Mvc\Model\TransactionInterface;

/**
 * Migration manager. Allows to migrate DATA to new version.
 * Use this only to migrate data. If you want to update database schema - @see \Engine\Db\Schema.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MigrationManager
{
    const
        /**
         * Migration file template name.
         */
        MIGRATION_NAME = 'Migration',
        MIGRATION_SUFFIX = '.php',
        MIGRATION_NAME_TEMPLATE = self::MIGRATION_NAME . '_%s',
        MIGRATION_TEMPLATE_PATH = 'migration' . DS,
        MIGRATION_PATH = self::MIGRATION_NAME . DS;

    const
        /**
         * Rollback all changes if was any error.
         */
        ROLLBACK_MODE_FULL = 'full',

        /**
         * Rollback only failed migrations.
         */
        ROLLBACK_MODE_FAILED = 'failed';

    use DIBehavior;

    /**
     * Migrate all modules.
     *
     * @param string $rollback Rollback mode.
     *
     * @return bool Migration success flag.
     */
    public function migrateAll($rollback = self::ROLLBACK_MODE_FULL)
    {
        $result = true;
        foreach ($this->getModules()->getPackages() as $module) {
            $result &= $this->migrate($module, $rollback);
        }

        return $result;
    }

    /**
     * Migrate specific module.
     *
     * @param PackageData $module   Module name.
     * @param string      $rollback Rollback mode.
     *
     * @return bool Migration success flag.
     */
    public function migrate($module, $rollback = self::ROLLBACK_MODE_FULL)
    {
        $result = true;
        $isConsole = $this->getApp()->isConsole();
        $migrations = $this->getMigrationsToMigrate($module);
        $transaction = $this->getTransactions()->get(false);

        /**
         * Start transaction if all migrations must be executed as one.
         * If one failed - all changes will be canceled.
         */
        if (self::ROLLBACK_MODE_FULL == $rollback) {
            $transaction->begin();
        }

        foreach ($migrations as $migration) {
            /** @var AbstractMigration $migrationClass */
            $migrationClass = $migration->getClass();
            $migrationClass = new $migrationClass();
            $migrationName = "{$module->getNameUpper()}/{$migration->getName()}";
            $this->getLogger()->info('[MIGRATION] ---> ' . $migrationName);

            /**
             * Show some info if running from console.
             */
            if ($isConsole) {
                print ConsoleUtils::text("$migrationName: ");
            }

            /**
             * If rollback mode is partial (only for failed migrations) than we must start
             * transaction only for current migration.
             */
            if (self::ROLLBACK_MODE_FAILED == $rollback) {
                $transaction->begin();
            }

            /**
             * Migrate.
             */
            try {
                $migrationClass->run();

                /**
                 * Show some info if running from console.
                 */
                if ($isConsole) {
                    print ConsoleUtils::info("OK", false, 0) . PHP_EOL;
                }

                /**
                 * Mark current migration as migrated.
                 */
                $this->_saveMigration($migration, $transaction);

                /**
                 * Commit current migration if partial rollback mode is used.
                 */
                if (self::ROLLBACK_MODE_FAILED == $rollback) {
                    $transaction->commit();
                }

                $result &= true;
            } catch (\Exception $ex) {
                try {
                    $transaction->rollback();
                } catch (\Exception $txFailed) {
                    // Silent.
                }

                /**
                 * Show some info if running from console.
                 */
                if ($isConsole) {
                    print ConsoleUtils::warn("FAILED", false, 0) . PHP_EOL;
                }

                /**
                 * If full mode of rollback - then go out, nothing to do here.
                 * Transaction was already cancelled in catch block.
                 */
                if (self::ROLLBACK_MODE_FULL == $rollback) {

                    if ($isConsole) {
                        print PHP_EOL;
                    }

                    return false;
                }

                $errorMessage = $ex->getMessage() . ': ' . PHP_EOL . $ex->getTraceAsString();
                $this->getLogger()->error($errorMessage);
                $this->getLogger('migrations')->error($errorMessage);
                $result &= false;
            }
        }

        /**
         * Commit all changes in current transaction if there was no errors.
         */
        if (self::ROLLBACK_MODE_FULL == $rollback) {
            $transaction->commit();
        }

        /**
         * Print new line in console if any migrations was processed.
         */
        if ($isConsole && !empty($migrations)) {
            print PHP_EOL;
        }

        return $result;
    }

    /**
     * Create new migration.
     *
     * @param string $module Module name in which migration must be created.
     *
     * @return string Migration file path.
     */
    public function create($module)
    {
        $module = $this->getModules()->get($module);
        $migrationName = sprintf(self::MIGRATION_NAME_TEMPLATE, date('Ymd_His'));
        $migrationPath = $module->getPath() . self::MIGRATION_PATH;
        $migrationFile = $migrationPath . $migrationName . self::MIGRATION_SUFFIX;

        $placeholders = ['%migrationClass%', '%nameUpper%'];
        $placeholdersValues = [$migrationName, $module->getNameUpper()];

        $migrationContent = str_replace(
            $placeholders,
            $placeholdersValues,
            file_get_contents(
                PackageGenerator::PACKAGE_TEMPLATES_LOCATION . self::MIGRATION_TEMPLATE_PATH . self::MIGRATION_NAME .
                self::MIGRATION_SUFFIX
            )
        );

        FileUtils::createIfMissing($migrationPath);
        file_put_contents($migrationFile, $migrationContent);

        return $migrationFile;
    }

    /**
     * Get status of migrations for all modules.
     *
     * @return array
     */
    public function getStatus()
    {
        $result = [];
        foreach ($this->getModules()->getPackages() as $module) {
            $migrations = $this->_getModuleMigrations($module);
            $migratedVersions = MigrationModel::findModuleMigratedVersion($module->getName());
            $existingVersions = array_map(
                function ($item) {
                    return $item->getVersion();
                },
                $migrations
            );

            $result[$module->getName()] = count(array_diff($existingVersions, $migratedVersions));
        }

        return $result;
    }

    /**
     * Get status of migrations for all modules.
     *
     * @param PackageData $module Module name.
     *
     * @return MigrationData[]
     */
    public function getMigrationsToMigrate($module)
    {
        $migrations = $this->_getModuleMigrations($module);
        $migratedVersions = MigrationModel::findModuleMigratedVersion($module->getName());

        return array_filter(
            $migrations,
            function ($item) use ($migratedVersions) {
                return !in_array($item->getVersion(), $migratedVersions);
            }
        );
    }

    /**
     * Get migrations defined in module.
     *
     * @param PackageData $module Module name.
     *
     * @return MigrationData[] Migrations in module.
     */
    protected function _getModuleMigrations($module)
    {
        $migrationPath = $module->getPath() . self::MIGRATION_PATH;
        $migrationFiles = FileUtils::globRecursive($migrationPath, '*.php');
        $result = [];
        foreach ($migrationFiles as $path) {
            $result[] = new MigrationData($module, $path);
        }

        return $result;
    }

    /**
     * Mark migration as migrated. Save it into database.
     *
     * @param MigrationData        $migration   Migration data.
     * @param TransactionInterface $transaction Current transaction.
     */
    protected function _saveMigration($migration, $transaction)
    {
        $migrationModel = new MigrationModel();
        $migrationModel->setTransaction($transaction);
        $migrationModel->module = $migration->getModule();
        $migrationModel->version = $migration->getVersion();
        $migrationModel->save();
    }
}