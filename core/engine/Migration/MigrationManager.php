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
use Engine\Form\Exception;
use Engine\Migration\Model\MigrationModel;
use Engine\Package\Exception\NoSuchPackageException;
use Engine\Package\PackageData;
use Engine\Package\PackageGenerator;
use Engine\Utils\FileUtils;

/**
 * Migration manager. Allows to migrate DATA to new version.
 * Use this only to migrate data. If you want to update database schema - use @see \Engine\Db\Schema.
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

    use DIBehavior;

    /**
     * Migrate all.
     */
    public function migrateAll()
    {
        foreach ($this->getDI()->getModules()->getPackages() as $module) {
            $this->migrate($module);
        }
    }

    /**
     * Migrate specific module.
     *
     * @param PackageData $module Module name.
     */
    public function migrate($module)
    {
        $migrations = $this->getMigrationsToMigrate($module);
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
        $module = $this->getDI()->getModules()->get($module);
        $migrationName = sprintf(self::MIGRATION_NAME_TEMPLATE, date('Ymd_His'));
        $migrationPath = $module->getPath() . self::MIGRATION_PATH;
        $migrationFile = $migrationPath . $migrationName . self::MIGRATION_SUFFIX;
        $migrationContent = str_replace(
            '%MigrationClass%',
            $migrationName,
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
        foreach ($this->getDI()->getModules()->getPackages() as $module) {
            $migrations = $this->_getModuleMigrations($module);
            $migratedVersions = MigrationModel::findModuleMigrations($module->getName());
            $existingVersions = array_map(function ($item) {
                return $item->getVersion();
            }, $migrations);

            $result[$module->getName()] = count(array_diff($existingVersions, $migratedVersions->toArray()));
        }

        return $result;
    }

    /**
     * Get status of migrations for all modules.
     *
     * @param PackageData $module Module name.
     *
     * @return array
     */
    public function getMigrationsToMigrate($module)
    {
        $migrations = $this->_getModuleMigrations($module);
        $migratedVersions = MigrationModel::findModuleMigrations($module->getName())->toArray();

        return array_filter(
            $migrations,
            function ($item) use ($migratedVersions) {
                return !in_array($item->getVersion(), $migratedVersions);
            });
    }

    /**
     * Get migrations defined in module.
     *
     * @param PackageData $module Module name.
     *
     * @return array Migrations in module.
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
}