<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
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

namespace Engine\Generator;

use Engine\Application;
use Engine\Generator\Migrations\Exception\MigrationExists;
use Engine\Generator\Migrations\Version as VersionItem;
use Engine\Generator\Migrations\Model as ModelMigration;

class Migrations
{
    public static function generateEmpty($options)
    {
        $version = self::_prepare($options);
        $migrationsDir = $options['migrationsDir'];

        $migration = ModelMigration::generateEmpty($version);
        file_put_contents($migrationsDir . '/' . $version . '/migrate.php', '<?php ' . PHP_EOL . PHP_EOL . $migration);
        return $version;
    }

    public static function generate($options)
    {
        $version = self::_prepare($options);
        $tableName = $options['tableName'];
        $isForModule = $options['isForModule'];
        $moduleName = $options['moduleName'];
        $migrationsDir = $options['migrationsDir'];

        if (!$isForModule) {
            if ($tableName == 'all') {
                $migrations = ModelMigration::generateAll($version);
                foreach ($migrations as $tableName => $migration) {
                    file_put_contents($migrationsDir . '/' . $version . '/' . $tableName . '.php', '<?php ' . PHP_EOL . PHP_EOL . $migration);
                }
            } else {
                $migration = ModelMigration::generate($version, $tableName);
                file_put_contents($migrationsDir . '/' . $version . '/' . $tableName . '.php', '<?php ' . PHP_EOL . PHP_EOL . $migration);
            }
        } else {
            $modelsDirectory = $migrationsDir . '/../Model';
            $di = \Phalcon\DI::getDefault();
            foreach (glob($modelsDirectory . '/*.php') as $modelPath) {
                $modelClass = '\\' . ucfirst($moduleName) . '\Model\\' . basename(str_replace('.php', '', $modelPath));
                $reflector = $di->get('annotations')->get($modelClass);

                // Get table name.
                $annotations = $reflector->getClassAnnotations();
                if ($annotations) {
                    foreach ($annotations as $annotation) {
                        if ($annotation->getName() == 'Source') {
                            $arguments = $annotation->getArguments();
                            $migration = ModelMigration::generate($version, $arguments[0]);
                            file_put_contents($migrationsDir . '/' . $version . '/' . $arguments[0] . '.php', '<?php ' . PHP_EOL . PHP_EOL . $migration);
                            break;
                        }
                    }
                }
            }
        }

        return $version;
    }

    /**
     * Run migrations
     */
    public static function run($options)
    {
        /** @var \Phalcon\Config $config */
        $config = $options['config'];
        $migrationsDir = $options['migrationsDir'];
        $toVersion = $options['toVersion'];

        if (isset($options['tableName'])) {
            $tableName = $options['tableName'];
        } else {
            $tableName = 'all';
        }

        if (!file_exists($migrationsDir)) {
            throw new \Exception('Migrations directory could not found');
        }

        $versions = array();
        $iterator = new \DirectoryIterator($migrationsDir);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                if (preg_match('/[a-z0-9](\.[a-z0-9]+)+/', $fileinfo->getFilename(), $matches)) {
                    $versions[] = new VersionItem($matches[0], 3);
                }
            }
        }

        if (count($versions) == 0) {
            throw new \Exception('Migrations were not found at ' . $migrationsDir);
        } else {
            $version = VersionItem::maximum($versions);
        }

        $migrationFid = $config->offsetExists('installedVersion');
        if ($migrationFid) {
            $fromVersion = $config->installedVersion;
        } else {
            $fromVersion = (string)$version;
        }

        if (isset($config->database)) {
            ModelMigration::setup($config->database);
        } else {
            throw new \Exception("Cannot load database configuration");
        }

        if ($toVersion) {
            $version = $toVersion;
        }

        $versionsBetween = VersionItem::between($fromVersion, $version, $versions);
        ModelMigration::setMigrationPath($migrationsDir . '/' . $version);
        $migrateType = ($fromVersion <= $version ? 'up' : 'down');
        foreach ($versionsBetween as $ver) {
            if ($tableName == 'all') {
                $iterator = new \DirectoryIterator($migrationsDir . '/' . $ver);
                $doLastMigration = false;
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        if ($fileinfo->getFilename() == 'migrate.php') {
                            $doLastMigration = true;
                            continue;
                        }
                        if (preg_match('/\.php$/', $fileinfo->getFilename())) {
                            ModelMigration::migrateFile((string)$ver, $migrationsDir . '/' . $ver . '/' . $fileinfo->getFilename(), $migrateType);
                        }
                    }
                }

                if ($doLastMigration) {
                    ModelMigration::migrateFile((string)$ver, $migrationsDir . '/' . $ver . '/' . 'migrate.php', $migrateType);
                }
            } else {
                $migrationPath = $migrationsDir . '/' . $ver . '/' . $tableName . '.php';
                if (file_exists($migrationPath)) {
                    ModelMigration::migrateFile((string)$ver, $migrationPath, $migrateType);
                } else {
                    throw new \Exception('Migration class was not found ' . $migrationPath);
                }
            }
        }

        $config->offsetSet('installedVersion', (string)$version);
        \Engine\Config::save($config);
        return $version;
    }

    protected static function _prepare($options)
    {
        $config = $options['config'];
        $migrationsDir = $options['migrationsDir'];
        $originalVersion = $options['originalVersion'];
        $force = $options['force'];

        if ($migrationsDir && !file_exists($migrationsDir)) {
            mkdir($migrationsDir);
        }

        if ($originalVersion) {

            if (!preg_match('/[a-z0-9](\.[a-z0-9]+)*/', $originalVersion, $matches)) {
                throw new \Exception('Version ' . $originalVersion . ' is invalid');
            }

            $originalVersion = $matches[0];
            $version = new VersionItem($originalVersion, 3);
            if (file_exists($migrationsDir . '/' . $version)) {
                if (!$force) {
                    throw new MigrationExists('Version ' . $version . ' is already generated. Try to use --force parameter.');
                }
            }
        } else {

            $versions = array();
            $iterator = new \DirectoryIterator($migrationsDir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isDir()) {
                    if (preg_match('/[a-z0-9](\.[a-z0-9]+)+/', $fileinfo->getFilename(), $matches)) {
                        $versions[] = new VersionItem($matches[0], 3);
                    }
                }
            }

            if (count($versions) == 0) {
                $version = new VersionItem('1.0.0');
            } else {
                $version = VersionItem::maximum($versions);
                $version = $version->addMinor(1);
            }
        }

        if (!file_exists($migrationsDir . '/' . $version)) {
            mkdir($migrationsDir . '/' . $version);
        }

        if (isset($config->database)) {
            ModelMigration::setup($config->database);
        } else {
            throw new \Exception("Cannot load database configuration");
        }

        ModelMigration::setMigrationPath($migrationsDir . '/' . $version);

        return $version;
    }

}