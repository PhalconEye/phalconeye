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

namespace Engine\Db;

use Engine\Behaviour\DIBehaviour;
use Engine\Exception as EngineException;
use Phalcon\Annotations\Collection;
use Phalcon\Db\AdapterInterface;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\DI;
use Phalcon\Mvc\Model\MetaData as PhalconMetadata;

/**
 * Schema generator.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Schema
{
    use DIBehaviour;

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
        DEFAULT_TABLE_COLLATION = 'utf8_general_ci';

    /**
     * Update database according to models metadata.
     *
     * @param bool $cleanup Drop not related tables.
     *
     * @return array
     */
    public function updateDatabase($cleanup = false)
    {
        $executedStatements = [];
        $processedTables = [];
        $references = [];

        /** @var AdapterInterface $db */
        $db = $this->getDI()->get('db');
        $defaultSchema = $this->getDI()->get('config')->database->dbname;

        foreach ($this->getAllModels() as $model) {
            $definition = $this->getModelMetadata($model['class']);
            $tableName = $definition['name'];
            $processedTables[] = $tableName;
            $counter = 0;

            if (isset($definition['references'])) {
                $references[$tableName] = $definition['references'];
                unset($definition['references']);
            }

            $tableExists = $db->tableExists($tableName, $defaultSchema);
            if ($tableExists == true) {
                $counter += $this->_modifyColumns($tableName, $defaultSchema, $definition);
                $counter += $this->_modifyIndexes($tableName, $defaultSchema, $definition);
            } else {
                $db->createTable($tableName, $defaultSchema, $definition);
                $counter++;
            }

            $executedStatements[$model['class']] = $counter;
        }

        // Process references
        $executedStatements['References'] = $this->_processReferences($defaultSchema, $references);

        if ($cleanup) {
            // Drop not existing tables.
            /** @var AdapterInterface $db */
            $db = $this->getDI()->get('db');
            foreach ($db->listTables() as $table) {
                if (!in_array($table, $processedTables)) {
                    $db->dropTable($table);
                    $executedStatements['Drop table `' . $table . '`'] = 1;
                }
            }
        }

        return $executedStatements;
    }

    /**
     * Update table according to model metadata.
     *
     * @param string $modelClass Full model name (with namespace).
     *
     * @throws \Exception
     *
     * @return array
     */
    public function updateTable($modelClass)
    {
        $counter = 0;
        $definition = $this->getModelMetadata($modelClass);
        $tableName = $definition['name'];

        /** @var AdapterInterface $db */
        $db = $this->getDI()->get('db');
        $defaultSchema = $this->getDI()->get('config')->database->dbname;

        // Prepare references
        $references = [];
        if (isset($definition['references'])) {
            $references = $definition['references'];
            unset($definition['references']);
        }

        $tableExists = $db->tableExists($tableName, $defaultSchema);
        if ($tableExists == true) {
            $counter += $this->_modifyColumns($tableName, $defaultSchema, $definition);
            $counter += $this->_modifyIndexes($tableName, $defaultSchema, $definition);
        } else {
            $db->createTable($tableName, $defaultSchema, $definition);
            $counter++;
        }

        $counter += $this->_processReferences($defaultSchema, [$tableName => $references]);

        return [$tableName => $counter];
    }

    /**
     * Get all models data: class name, path, module, etc.
     *
     * @return array
     */
    public function getAllModels()
    {
        $modelsInfo = [];
        $registry = $this->getDI()->get('registry');
        foreach ($registry->modules as $module) {
            $module = ucfirst($module);
            $modelsDirectory = $registry->directories->modules . $module . '/Model';
            foreach (glob($modelsDirectory . '/*.php') as $modelPath) {
                $modelsInfo[] = [
                    'class' => '\\' . $module . '\Model\\' . basename(str_replace('.php', '', $modelPath)),
                    'path' => $modelPath,
                    'module' => $module
                ];
            }
        }

        return $modelsInfo;
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
        /** @var \Phalcon\Annotations\Reflection $reflector */
        $reflector = $this->getDI()->get('annotations')->get($modelClass);
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
        $annotations = $reflector->getClassAnnotations();
        if ($annotations) {
            /** @var \Phalcon\Annotations\Annotation $annotation */
            foreach ($annotations as $annotation) {
                if ($annotation->getName() == 'Source') {
                    $arguments = $annotation->getArguments();
                    $metadata['name'] = $arguments[0];
                } elseif ($annotation->getName() == 'BelongsTo') {
                    $references[] = $annotation->getArguments();
                }
            }
        }

        $properties = $reflector->getPropertiesAnnotations();
        foreach ($properties as $name => $collection) {
            if ($collection->has('Column')) {
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
                    $indexes[$arguments[0]][] = $columnName;
                }

                $metadata['columns'][] = new Column($columnName, $columnData);
            }
        }

        /**
         * Setup indexes objects.
         */
        $metadata['indexes'][] = new Index('PRIMARY', $primary);
        foreach ($indexes as $indexName => $fields) {
            $metadata['indexes'][implode('_', $fields)] = new Index($indexName, $fields);
        }

        /**
         * Setup references.
         */
        foreach ($references as $reference) {
            if (empty($reference[0]) || empty($reference[1]) || empty($reference[2]) || !class_exists($reference[1])) {
                throw new EngineException("Bad reference for model {$modelClass}: (" . implode(', ', $reference) . ')');
                continue;
            }

            $uniqName = $modelClass::getTableName() . '-' .
                $reference[1]::getTableName() . '-' .
                $reference[0] . '-' .
                $reference[2];
            $metadata['references'][] = new Reference(
                'fk-' . $uniqName,
                [
                    "referencedTable" => $reference[1]::getTableName(),
                    "columns" => [$reference[0]],
                    "referencedColumns" => [$reference[2]],
                ]
            );
            // Add FK index.
            $metadata['indexes'][$reference[0]] = new Index('fki-' . $uniqName, [$reference[0]]);
        }

        return $metadata;
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
            $columnData['identity'] = true;
            $columnData['first'] = true;
            if (isset($columnData['isNumeric']) && $columnData['isNumeric'] == true) {
                $columnData['autoIncrement'] = true;
            }
        }

        return $columnData;
    }

    /**
     * Modify table columns.
     *
     * @param string $tableName  Table name.
     * @param string $schemaName Schema name.
     * @param array  $definition Table Definition.
     *
     * @return int
     * @throws \Exception
     */
    protected function _modifyColumns($tableName, $schemaName, $definition)
    {
        if (empty($definition['columns'])) {
            throw new \Exception('Table must have at least one column');
        }

        $counter = 0;
        $db = $this->getDI()->get('db');

        $fields = [];
        foreach ($definition['columns'] as $tableColumn) {
            if (!is_object($tableColumn)) {
                throw new \Exception('Wrong column definition, it must be a object');
            }
            $fields[$tableColumn->getName()] = $tableColumn;
        }

        $localFields = [];
        $description = $db->describeColumns($tableName, $schemaName);
        foreach ($description as $field) {
            $localFields[$field->getName()] = $field;
        }

        foreach ($fields as $fieldName => $tableColumn) {
            if (!isset($localFields[$fieldName])) {
                $db->addColumn($tableName, $tableColumn->getSchemaName(), $tableColumn);
                $counter++;
            } else {

                $changed = false;

                if ($localFields[$fieldName]->getType() != $tableColumn->getType()) {
                    $changed = true;
                }

                if ($localFields[$fieldName]->getSize() != $tableColumn->getSize()) {
                    $changed = true;
                }

                if ($tableColumn->isNotNull() != $localFields[$fieldName]->isNotNull()) {
                    $changed = true;
                }

                if ($changed == true) {
                    $db->modifyColumn($tableName, $tableColumn->getSchemaName(), $tableColumn);
                    $counter++;
                }
            }
        }

        foreach (array_keys($localFields) as $fieldName) {
            if (!isset($fields[$fieldName])) {
                $db->dropColumn($tableName, null, $fieldName);
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Modify indexes.
     *
     * @param string $tableName  Table name.
     * @param string $schemaName Schema name.
     * @param array  $definition Table Definition.
     *
     * @return int
     */
    protected function _modifyIndexes($tableName, $schemaName, $definition)
    {
        if (empty($definition['indexes'])) {
            return 0;
        }

        $counter = 0;
        $indexes = [];
        $db = $this->getDI()->get('db');

        $localIndexes = [];
        $actualIndexes = $db->describeIndexes($tableName, $schemaName);
        foreach ($actualIndexes as $actualIndex) {
            $localIndexes[$actualIndex->getName()] = $actualIndex->getColumns();
        }

        foreach ($definition['indexes'] as $tableIndex) {
            $indexes[$tableIndex->getName()] = $tableIndex;
            if (!isset($localIndexes[$tableIndex->getName()])) {
                if ($tableIndex->getName() == 'PRIMARY') {
                    $db->addPrimaryKey($tableName, $schemaName, $tableIndex);
                    $counter++;
                } else {
                    $db->addIndex($tableName, $schemaName, $tableIndex);
                    $counter++;
                }
            } else {
                $changed = false;
                if (count($tableIndex->getColumns()) != count($localIndexes[$tableIndex->getName()])) {
                    $changed = true;
                } else {
                    foreach ($tableIndex->getColumns() as $columnName) {
                        if (!in_array($columnName, $localIndexes[$tableIndex->getName()])) {
                            $changed = true;
                            break;
                        }
                    }
                }
                if ($changed == true) {
                    if ($tableIndex->getName() == 'PRIMARY') {
                        $db->dropPrimaryKey($tableName, $schemaName);
                        $db->addPrimaryKey($tableName, $schemaName, $tableIndex);
                        $counter += 2;
                    } else {
                        $db->dropIndex($tableName, $schemaName, $tableIndex->getName());
                        $db->addIndex($tableName, $schemaName, $tableIndex);
                        $counter += 2;
                    }
                }
            }
        }
        foreach (array_keys($localIndexes) as $indexName) {
            if (!isset($indexes[$indexName])) {
                $db->dropIndex($tableName, null, $indexName);
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Do actions with references.
     *
     * @param string $schemaName           Database name.
     * @param array  $referencesDefinition References.
     *
     * @return array
     */
    protected function _processReferences($schemaName, $referencesDefinition)
    {
        if (!is_array($referencesDefinition) || empty($referencesDefinition)) {
            return 0;
        }

        $counter = 0;
        $db = $this->getDI()->get('db');

        foreach ($referencesDefinition as $tableName => $definition) {
            if (empty($definition)) {
                continue;
            }

            $references = [];
            $localReferences = [];
            $activeReferences = $db->describeReferences($tableName, $schemaName);
            foreach ($activeReferences as $activeReference) {
                $localReferences[$activeReference->getName()] = [
                    'referencedTable' => $activeReference->getReferencedTable(),
                    'columns' => $activeReference->getColumns(),
                    'referencedColumns' => $activeReference->getReferencedColumns(),
                ];
            }

            foreach ($definition as $tableReference) {
                $references[$tableReference->getName()] = $tableReference;
                if (!isset($localReferences[$tableReference->getName()])) {
                    // Add new reference, that isn't exists.
                    $db->addForeignKey($tableName, $tableReference->getSchemaName(), $tableReference);
                    $counter++;
                } else {
                    // Change reference.
                    $changed = $this->_checkReferenceChanges($tableReference, $localReferences);

                    if ($changed == true) {
                        $db->dropForeignKey($tableName, $tableReference->getSchemaName(), $tableReference->getName());
                        $db->addForeignKey($tableName, $tableReference->getSchemaName(), $tableReference);
                        $counter += 2;
                    }
                }
            }

            foreach (array_keys($localReferences) as $referenceName) {
                // Drop reference.
                if (!isset($references[$referenceName])) {
                    $db->dropForeignKey($tableName, null, $referenceName);
                    $counter++;
                }
            }
        }

        return $counter;
    }

    /**
     * Check if current reference has changes.
     *
     * @param Reference $tableReference  Table reference.
     * @param array     $localReferences Local references.
     *
     * @return bool
     */
    protected function _checkReferenceChanges($tableReference, $localReferences)
    {
        if (
            $tableReference->getReferencedTable() !=
            $localReferences[$tableReference->getName()]['referencedTable']
        ) {
            return true;
        }


        if (
            count($tableReference->getColumns()) !=
            count($localReferences[$tableReference->getName()]['columns'])
        ) {
            return true;
        }


        if (
            count($tableReference->getReferencedColumns()) !=
            count($localReferences[$tableReference->getName()]['referencedColumns'])
        ) {
            return true;
        }


        foreach ($tableReference->getColumns() as $columnName) {
            if (!in_array($columnName, $localReferences[$tableReference->getName()]['columns'])) {
                return true;
                break;
            }
        }


        foreach ($tableReference->getReferencedColumns() as $columnName) {
            if (
            !in_array($columnName, $localReferences[$tableReference->getName()]['referencedColumns'])
            ) {
                return true;
                break;
            }
        }

        return false;
    }
}
