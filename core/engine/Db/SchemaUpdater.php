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

namespace Engine\Db;

use Engine\Behavior\DIBehavior;
use Engine\Db\Data\UpdateData;
use Engine\Db\Data\UpdateStatementData;
use Engine\Exception as EngineException;
use Engine\Migration\Model\MigrationModel;
use Engine\Package\PackageManager;
use Phalcon\Annotations\Collection;
use Phalcon\Annotations\Reflection;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\MetaData as PhalconMetadata;

/**
 * Schema updater.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class SchemaUpdater
{
    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    CONST
        /**
         * Default table type.
         */
        DEFAULT_TABLE_TYPE = 'BASE TABLE',

        /**
         * Default table engine.
         */
        DEFAULT_ENGINE_TYPE = 'InnoDB',

        /**
         * Default table collation.
         */
        DEFAULT_TABLE_COLLATION = 'utf8_general_ci',

        /**
         * Default index type.
         */
        DEFAULT_INDEX_TYPE = 'UNIQUE',

        /**
         * Core data type.
         */
        DATA_FILE_TYPE_CORE = 'core',

        /**
         * Sample data type.
         */
        DATA_FILE_TYPE_SAMPLE = 'sample',

        /**
         * Data file template.
         */
        DATA_FILE_TEMPLATE = 'Assets/sql/%s.sql',

        /**
         * Determines end of query in data files.
         */
        SQL_QUERY_END = '--END';

    private $_schemaName;

    /**
     * Table updater constructor.
     *
     * @param DIBehavior|DiInterface $di     Dependency injection.
     * @param string                 $schema Schema name.
     */
    public function __construct($di, $schema)
    {
        $this->__DIConstruct($di);
        $this->_schemaName = $schema;
    }

    /**
     * Get schema name.
     *
     * @return string
     */
    public function getSchemaName(): string
    {
        return $this->_schemaName;
    }

    /**
     * Initialize database.
     *
     * @param bool $importCoreData   Import core data from all modules.
     * @param bool $importSampleData Import sample data from all modules.
     *
     * @return UpdateData[]
     */
    public function initialize(bool $importCoreData = true, bool $importSampleData = false)
    {
        $time = microtime(true);
        $logger = $this->getLogger();
        $this->_logHeader('Initialization');
        $logger->info('Dropping all tables...');
        $this->getDb()->query(
            sprintf(
                "
                SET FOREIGN_KEY_CHECKS = 0;
                SET @tables = NULL;
                SELECT GROUP_CONCAT(table_schema, '.', table_name) INTO @tables
                  FROM information_schema.tables
                  WHERE table_schema = '%s';

                SET @tables = CONCAT('DROP TABLE ', @tables);
                PREPARE stmt FROM @tables;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ",
                $this->getSchemaName()
            )
        );
        $logger->info('Done!' . PHP_EOL);

        $result = $this->update();
        $this->getDb()->query('SET FOREIGN_KEY_CHECKS = 1;');

        if ($importCoreData) {
            $this->_logHeader('Core Data');

            foreach ($this->getModules()->getPackages() as $module) {
                $this->_importData($module->getPath(), self::DATA_FILE_TYPE_CORE);
            }

            $logger->info('Done!' . PHP_EOL);
        }

        if ($importSampleData) {
            $this->_logHeader('Sample Data');

            foreach ($this->getModules()->getPackages() as $module) {
                $this->_importData($module->getPath(), self::DATA_FILE_TYPE_SAMPLE);
            }

            $logger->info('Done!' . PHP_EOL);
        }

        $end = round(microtime(true) - $time);
        $this->_logHeader(sprintf('Initialization completed in %ss', $end));

        return $result;
    }

    /**
     * Update database according to models metadata.
     *
     * @return UpdateData[]|string
     */
    public function update()
    {
        $logger = $this->getLogger();
        $this->_logHeader('Update database');

        $transaction = $this->getTransactions()->get(false);
        $transaction->begin();

        $result = [];

        try {
            foreach ($this->getAllModels() as $model) {
                $result += $this->_updateTable($model['class']);
            }
        } catch (\Exception $ex) {
            try {
                $transaction->rollback();
            } catch (\Exception $txFailed) {
                // Silent.
            }

            $message = "Failed to update database: " . $ex->getMessage();
            $logger->exception($ex, $message);

            return $message;
        }

        $logger->info('Done!' . PHP_EOL);
        return $result;
    }

    /**
     * Update one table according to model metadata.
     *
     * @param string $modelClass Model class. Example: \Test\Model\ClassModel.
     *
     * @return UpdateData[]|string
     */
    public function updateTable($modelClass)
    {
        $logger = $this->getLogger();
        $this->_logHeader("Updating $modelClass");

        $transaction = $this->getTransactions()->get(false);
        $transaction->begin();

        try {
            $result = $this->_updateTable($modelClass);
            $logger->info('Done!' . PHP_EOL);
            return $result;
        } catch (\Exception $ex) {
            try {
                $transaction->rollback();
            } catch (\Exception $txFailed) {
                // Silent.
            }

            $message = "Failed to update table: " . $ex->getMessage();
            $logger->exception($ex, $message);

            return $message;
        }
    }

    /**
     * Remove unused tables, columns, relations, etc.
     *
     * @return UpdateData[]|string
     */
    public function cleanupDatabase()
    {
        $logger = $this->getLogger();
        $this->_logHeader('Cleanup database');
        $transaction = $this->getTransactions()->get(false);
        $transaction->begin();

        $result = [];

        $db = $this->getDI()->getDb();
        $tablesInDatabase = $db->listTables();
        $tablesInModel = [];

        try {
            foreach ($this->getAllModels() as $model) {
                $table = new TableUpdater($this, $model['class']);

                // Drop unused references.
                $table->cleanupReferences();

                // Drop unused indexes.
                $table->cleanupIndexes();

                // Drop unused columns.
                $table->cleanupColumns();

                $tablesInModel[] = $table->getTableName();
                $result[$table->getTableName()] = $table->getResult();
            }

            // Drop table if it is missing.
            foreach ($tablesInDatabase as $table) {
                if (!in_array($table, $tablesInModel)) {
                    $dropResult = $db->dropTable($table);
                    $dropResultData = new UpdateData();
                    $dropResultData->add(UpdateStatementData::OBJ_TABLE, UpdateStatementData::STMT_DROP, $dropResult);
                    $result[$table] = $dropResultData;
                }
            }
        } catch (\Exception $ex) {
            try {
                $transaction->rollback();
            } catch (\Exception $txFailed) {
                // Silent.
            }

            $message = "Failed to cleanup database: " . $ex->getMessage();
            $logger->exception($ex, $message);

            return $message;
        }

        $logger->info('Done!' . PHP_EOL);
        return $result;
    }

    /**
     * Get all models data: class name, path, module, etc.
     *
     * @return array
     */
    public function getAllModels()
    {
        $modelsInfo = [];
        foreach ($this->getModules()->getPackages() as $module) {
            $modelsDirectory = $module->getPath() . 'Model' . DS;
            foreach (glob($modelsDirectory . '*.php') as $modelPath) {
                $modelsInfo[] = [
                    'class' => PackageManager::SEPARATOR_NS .
                        $module->getName() .
                        '\Model\\' .
                        basename(str_replace('.php', '', $modelPath)),
                    'path' => $modelPath,
                    'module' => $module->getName()
                ];
            }
        }

        return array_merge($this->getEngineModels(), $modelsInfo);
    }

    /**
     * Get models located in engine.
     *
     * @return array List of engine models.
     */
    public function getEngineModels()
    {
        return [
            [
                'class' => MigrationModel::CLASS,
                'path' => (new \ReflectionClass(MigrationModel::CLASS))->getFileName()
            ]
        ];
    }

    /**
     * Get model metadata by model class.
     *
     * @param string $modelClass Model class name (with namespace).
     *
     * @return array
     * @throws EngineException
     */
    public function getModelMetadata($modelClass)
    {
        $reflector = $this->getDI()->getAnnotations()->get($modelClass);
        $metadata = [
            'name' => '',
            'columns' => [],
            'indexes' => [],
            'references' => [],
            'options' => [
                'TABLE_TYPE' => self::DEFAULT_TABLE_TYPE,
                'ENGINE' => self::DEFAULT_ENGINE_TYPE,
                'TABLE_COLLATION' => self::DEFAULT_TABLE_COLLATION
            ]
        ];
        $indexes = [];
        $primary = [];
        $references = [];

        // Get table name and references data.
        if ($annotations = $reflector->getClassAnnotations()) {
            foreach ($annotations as $annotation) {
                if ($annotation->getName() == 'Source') {
                    $arguments = $annotation->getArguments();
                    $metadata['name'] = $arguments[0];
                } elseif ($annotation->getName() == 'BelongsTo') {
                    $references[] = $annotation->getArguments();
                }
            }
        }

        $data = $this->_getPropertiesAnnotations($reflector, $metadata, $primary, $indexes);
        $metadata = $data['metadata'];
        $primary = $data['primary'];
        $indexes = $data['indexes'];

        /**
         * Setup indexes objects.
         */
        $metadata['indexes'][] = new Index('PRIMARY', $primary);
        foreach ($indexes as $indexName => $types) {
            foreach ($types as $type => $fields) {
                $metadata['indexes'][implode('_', $fields)] = new Index($indexName, $fields, $type);
            }
        }

        /**
         * Setup references.
         */
        foreach ($references as $reference) {
            if (empty($reference[0]) || empty($reference[1]) || empty($reference[2]) || !class_exists($reference[1])) {
                throw new EngineException("Bad reference for model {$modelClass}: (" . implode(', ', $reference) . ')');
            }

            $uniqueName = $modelClass::getTableName() . '-' .
                $reference[1]::getTableName() . '-' .
                $reference[0] . '-' .
                $reference[2];
            $metadata['references'][] = new Reference(
                'fk-' . $uniqueName,
                [
                    "referencedTable" => $reference[1]::getTableName(),
                    "columns" => [$reference[0]],
                    "referencedColumns" => [$reference[2]],
                ]
            );
            // Add FK index.
            $metadata['indexes'][$reference[0]] = new Index('fki-' . $uniqueName, [$reference[0]]);
        }

        return $metadata;
    }

    /**
     * Update table according to model metadata.
     *
     * @param string $modelClass Model class. Example: \Test\Model\ClassModel.
     *
     * @return UpdateData[]
     */
    protected function _updateTable($modelClass)
    {
        $table = new TableUpdater($this, $modelClass);
        $tableName = $table->getTableName();
        $this->getLogger()->info("[$tableName]");
        return [$table->getTableName() => $table->update()];
    }

    /**
     * Get properties annotations.
     *
     * @param Reflection $reflector Reflector object.
     * @param array      $metadata  Metadata.
     * @param array      $primary   Primary keys.
     * @param array      $indexes   Indexes.
     *
     * @return array
     */
    protected function _getPropertiesAnnotations(Reflection $reflector, array $metadata, array $primary, array $indexes)
    {
        foreach ($reflector->getPropertiesAnnotations() as $name => $collection) {
            if (!$collection->has('Column')) {
                continue;
            }

            $arguments = $collection->get('Column')->getArguments();
            /**
             * Get the column's name.
             */
            $columnName = $this->_getColumnName($name, $arguments);
            $columnData = $this->_getModelColumnData($arguments, $collection);

            /**
             * Check if the attribute is marked as primary.
             */
            if ($collection->has('Primary')) {
                $primary[] = $columnName;
            }

            /**
             * Check index.
             */
            if ($collection->has('Index')) {
                $arguments = $collection->get('Index')->getArguments();
                $type = isset($arguments[1]) ? $arguments[1] : self::DEFAULT_INDEX_TYPE;

                $indexes[$arguments[0]][$type][] = $columnName;
            }

            $metadata['columns'][] = new Column($columnName, $columnData);
        }

        return [
            'metadata' => $metadata,
            'primary' => $primary,
            'indexes' => $indexes
        ];
    }

    /**
     * Choose column name.
     *
     * @param string $name      Column name from code.
     * @param array  $arguments Column arguments.
     *
     * @return mixed
     */
    protected function _getColumnName($name, $arguments)
    {
        if (isset($arguments['column'])) {
            return $arguments['column'];
        } else {
            return $name;
        }
    }

    /**
     * Get column info.
     *
     * @param array      $arguments  Annotations arguments.
     * @param Collection $collection Annotations collection.
     *
     * @return array
     */
    protected function _getModelColumnData($arguments, $collection)
    {
        $columnData = [];

        /**
         * Get type.
         */
        if (isset($arguments['type'])) {
            switch ($arguments['type']) {
                case 'integer':
                    $columnData['type'] = Column::TYPE_INTEGER;
                    $columnData['isNumeric'] = true;
                    break;
                case 'string':
                    $columnData['type'] = Column::TYPE_VARCHAR;
                    break;
                case 'text':
                    $columnData['type'] = Column::TYPE_TEXT;
                    break;
                case 'boolean':
                    $columnData['type'] = Column::TYPE_BOOLEAN;
                    break;
                case 'date':
                    $columnData['type'] = Column::TYPE_DATE;
                    break;
                case 'datetime':
                    $columnData['type'] = Column::TYPE_DATETIME;
                    break;
            }
        }

        /**
         * Get size.
         */
        if (isset($arguments['size'])) {
            $columnData['size'] = $arguments['size'];
        }

        /**
         * Check for the 'nullable' parameter in the 'Column' annotation.
         */
        if (isset($arguments['nullable'])) {
            $columnData['notNull'] = !$arguments['nullable'];
        }

        /**
         * Check if the attribute is marked as identity.
         */
        if ($collection->has('Identity')) {
            $columnData['first'] = true;
            if (isset($columnData['isNumeric']) && $columnData['isNumeric'] == true) {
                $columnData['autoIncrement'] = true;
            }
        }

        return $columnData;
    }

    /**
     * Import data file (sql file).
     *
     * @param string $path     Path to module.
     * @param string $dataType Data type (core|sample).
     */
    private function _importData($path, $dataType)
    {
        $file = $path . sprintf(self::DATA_FILE_TEMPLATE, $dataType);
        if (!file_exists($file)) {
            return;
        }

        $logger = $this->getLogger();
        $logger->info(sprintf('Importing %s...', $file));

        $transaction = $this->getTransactions()->get(false);
        $transaction->begin();

        try {
            // Separate sql file on queries.
            // This is required for errors catching.
            // See https://bugs.php.net/bug.php?id=61613.
            $queries = explode(self::SQL_QUERY_END, file_get_contents($file));
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) {
                    continue;
                }
                $this->getDb()->execute($query);
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            try {
                $transaction->rollback();
            } catch (\Exception $txFailed) {
                // Silent.
            }

            $message = "Failed to import file: " . $file . PHP_EOL . $ex->getMessage();
            $logger->exception($ex, $message);
        }
    }

    /**
     * Log header info.
     *
     * @param string $message Message in header.
     */
    private function _logHeader($message)
    {
        $logger = $this->getLogger();
        $logger->info('================================================================');
        $logger->info("| $message");
        $logger->info('================================================================');
    }
}
