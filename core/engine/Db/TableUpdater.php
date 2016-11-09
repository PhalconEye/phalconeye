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
use Phalcon\Db\Column;
use Phalcon\Db\Reference;

/**
 * Table updater.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class TableUpdater
{
    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    private $_modelClass;
    private $_schemaName;
    private $_metadata;
    private $_tableName;
    private $_db;

    /** @var UpdateData */
    private $_result;

    /**
     * Table updater constructor.
     *
     * @param SchemaUpdater $schemaUpdater Dependency injection.
     * @param string        $modelClass    Model class.
     */
    public function __construct(SchemaUpdater $schemaUpdater, $modelClass)
    {
        $this->__DIConstruct($schemaUpdater->getDI());
        $this->_schemaName = $schemaUpdater->getSchemaName();
        $this->_metadata = $schemaUpdater->getModelMetadata($modelClass);

        $this->_modelClass = $modelClass;
        $this->_tableName = $this->_metadata['name'];
        $this->_result = new UpdateData();
        $this->_db = $this->getDb();
    }

    /**
     * Model class.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->_modelClass;
    }

    /**
     * Schema name.
     *
     * @return string
     */
    public function getSchemaName(): string
    {
        return $this->_schemaName;
    }

    /**
     * Model metadata.
     *
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Table name.
     *
     * @return mixed
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Update result.
     *
     * @return UpdateData
     */
    public function getResult(): UpdateData
    {
        return $this->_result;
    }

    /**
     * Update table according to model metadata.
     *
     * @throws \Exception
     *
     * @return UpdateData
     */
    public function update()
    {
        if ($this->_db->tableExists($this->getTableName(), $this->getSchemaName())) {
            $this->_modifyColumns();
            $this->_modifyIndexes();
        } else {
            $this->_createTable();

        }

        $this->_updateReferences();

        return $this->getResult();
    }

    /**
     * Drop table from database.
     */
    public function drop()
    {
        $result = $this->_db->dropTable($this->getTableName(), $this->getSchemaName());
        $this->_result->add(UpdateStatementData::OBJ_TABLE, UpdateStatementData::STMT_DROP, $result);
        $this->_log('DROP TABLE %s', $this->getTableName());
    }

    /**
     * Cleanup unused references.
     */
    public function cleanupReferences()
    {
        $referencesInModel = $this->_getReferencesFromModel();
        $referencesInDatabase = $this->_getReferencesFromDatabase();

        foreach (array_keys($referencesInDatabase) as $referenceName) {
            // Drop reference.
            if (!isset($referencesInModel[$referenceName])) {
                $result = $this->_db->dropForeignKey($this->getTableName(), $this->getSchemaName(), $referenceName);
                $this->_result->add(UpdateStatementData::OBJ_REF, UpdateStatementData::STMT_DROP, $result);
                $this->_log('DROP REFERENCE %s', $referenceName);
            }
        }
    }

    /**
     * Cleanup unused indexes.
     */
    public function cleanupIndexes()
    {
        $indexesInModel = $this->_getIndexesFromModel();
        $indexesInDatabase = $this->_getIndexesFromDatabase();

        foreach (array_keys($indexesInDatabase) as $indexName) {
            if (!isset($indexesInModel[$indexName])) {
                $result = $this->_db->dropIndex($this->getTableName(), $this->getSchemaName(), $indexName);
                $this->_result->add(UpdateStatementData::OBJ_INDEX, UpdateStatementData::STMT_DROP, $result);
                $this->_log('DROP INDEX %s', $indexName);
            }
        }
    }

    /**
     * Cleanup unused columns.
     */
    public function cleanupColumns()
    {
        $columnsInModel = $this->_getColumnsFromModel();
        $columnsInDatabase = $this->_getColumnsFromDatabase();

        foreach (array_keys($columnsInDatabase) as $modelFieldName) {
            if (!isset($columnsInModel[$modelFieldName])) {
                $result = $this->_db->dropColumn($this->getTableName(), $this->getSchemaName(), $modelFieldName);
                $this->_result->add(UpdateStatementData::OBJ_COLUMN, UpdateStatementData::STMT_DROP, $result);
                $this->_log('DROP COLUMN %s', $modelFieldName);
            }
        }
    }

    /**
     * Create table in database.
     */
    protected function _createTable()
    {
        $result = $this->_db->createTable($this->getTableName(), $this->getSchemaName(), $this->getMetadata());
        $this->_result->add(UpdateStatementData::OBJ_TABLE, UpdateStatementData::STMT_CREATE, $result);
        $this->_log('CREATE TABLE %s', $this->getTableName());
    }

    /**
     * Modify table columns.
     *
     * @throws \Exception
     */
    protected function _modifyColumns()
    {
        $columnsInModel = $this->_getColumnsFromModel();
        $columnsInDatabase = $this->_getColumnsFromDatabase();

        /** @var Column $modelColumn */
        foreach ($columnsInModel as $modelFieldName => $modelColumn) {
            if (!isset($columnsInDatabase[$modelFieldName])) {
                $result = $this->_db->addColumn($this->getTableName(), $modelColumn->getSchemaName(), $modelColumn);
                $this->_result->add(UpdateStatementData::OBJ_COLUMN, UpdateStatementData::STMT_CREATE, $result);
                $this->_log('ADD COLUMN %s', $modelColumn->getName());
            } else {
                /** @var Column $fieldInDatabase */
                $fieldInDatabase = $columnsInDatabase[$modelFieldName];
                $changed = false;
                $data = $this->_getData($fieldInDatabase);

                /**
                 * Update column only if size changed and it's bigger then previous.
                 * If size will be lower a truncation error is possible.
                 */
                if (
                    $fieldInDatabase->getSize() != $modelColumn->getSize() &&
                    $fieldInDatabase->getSize() < $modelColumn->getSize()
                ) {
                    $data["size"] = $modelColumn->getSize();
                    $changed = true;
                }

                /**
                 * Update column only if not null changed and changed to false.
                 * If was null allowed and became not null - the error (possible) will happen during update.
                 */
                if (
                    $fieldInDatabase->isNotNull() != $modelColumn->isNotNull() &&
                    $modelColumn->isNotNull() === false
                ) {
                    $data["notNull"] = $modelColumn->isNotNull();
                    $changed = true;
                }

                if ($changed) {
                    if (!$this->_isColumnScaleAllowed($fieldInDatabase)) {
                        unset($data['scale']);
                    }

                    $column = new Column($fieldInDatabase->getName(), $data);
                    $result = $this->_db->modifyColumn($this->getTableName(), $column->getSchemaName(), $column);
                    $this->_result->add(UpdateStatementData::OBJ_COLUMN, UpdateStatementData::STMT_MODIFY, $result);
                    $this->_log('MODIFY COLUMN %s', $column->getName());
                }
            }
        }
    }

    /**
     * Modify indexes.
     */
    protected function _modifyIndexes()
    {
        $indexesInModel = $this->_getIndexesFromModel();
        $indexesInDatabase = $this->_getIndexesFromDatabase();
        foreach ($indexesInModel as $indexInModel) {
            if (!isset($indexesInDatabase[$indexInModel->getName()])) {
                if ($indexInModel->getName() == 'PRIMARY') {
                    $result = $this->_db->addPrimaryKey($this->getTableName(), $this->getSchemaName(), $indexInModel);
                } else {
                    $result = $this->_db->addIndex($this->getTableName(), $this->getSchemaName(), $indexInModel);
                }
                $this->_result->add(UpdateStatementData::OBJ_INDEX, UpdateStatementData::STMT_CREATE, $result);
                $this->_log('CREATE INDEX %s', $indexInModel->getName());
            } else {
                $changed = false;
                if (count($indexInModel->getColumns()) != count($indexesInDatabase[$indexInModel->getName()])) {
                    $changed = true;
                } else {
                    foreach ($indexInModel->getColumns() as $columnName) {
                        if (!in_array($columnName, $indexesInDatabase[$indexInModel->getName()])) {
                            $changed = true;
                            break;
                        }
                    }
                }
                if ($changed == true) {
                    if ($indexInModel->getName() == 'PRIMARY') {
                        $r1 = $this->_db->dropPrimaryKey($this->getTableName(), $this->getSchemaName());
                        $r2 = $this->_db->addPrimaryKey($this->getTableName(), $this->getSchemaName(), $indexInModel);

                    } else {
                        $r1 = $this->_db->dropIndex(
                            $this->getTableName(), $this->getSchemaName(), $indexInModel->getName()
                        );
                        $r2 = $this->_db->addIndex($this->getTableName(), $this->getSchemaName(), $indexInModel);
                    }

                    $this->_result->add(UpdateStatementData::OBJ_INDEX, UpdateStatementData::STMT_DROP, $r1);
                    $this->_result->add(UpdateStatementData::OBJ_INDEX, UpdateStatementData::STMT_CREATE, $r2);
                    $this->_log('DROP INDEX %s', $indexInModel->getName());
                    $this->_log('CREATE INDEX %s', $indexInModel->getName());
                }
            }
        }
    }

    /**
     * Do actions with references.
     *
     * @return integer
     */
    protected function _updateReferences()
    {
        $referencesInModel = $this->_getReferencesFromModel();
        $referencesInDatabase = $this->_getReferencesFromDatabase();

        foreach ($referencesInModel as $referenceInModel) {
            if (!isset($referencesInDatabase[$referenceInModel->getName()])) {
                // Add new reference, that isn't exists.
                $result = $this->_db->addForeignKey(
                    $this->getTableName(), $referenceInModel->getSchemaName(), $referenceInModel
                );
                $this->_result->add(UpdateStatementData::OBJ_REF, UpdateStatementData::STMT_CREATE, $result);
                $this->_log('CREATE REFERENCE %s', $referenceInModel->getName());
            } else {
                // Change reference.
                $changed = $this->_checkReferenceChanges($referenceInModel, $referencesInDatabase);

                if ($changed == true) {
                    $result = $this->_db->dropForeignKey(
                        $this->getTableName(),
                        $referenceInModel->getSchemaName(),
                        $referenceInModel->getName()
                    );
                    $this->_result->add(UpdateStatementData::OBJ_REF, UpdateStatementData::STMT_DROP, $result);
                    $this->_log('DROP REFERENCE %s', $referenceInModel->getName());

                    $result = $this->_db->addForeignKey(
                        $this->getTableName(),
                        $referenceInModel->getSchemaName(),
                        $referenceInModel
                    );
                    $this->_result->add(UpdateStatementData::OBJ_REF, UpdateStatementData::STMT_CREATE, $result);
                    $this->_log('CREATE REFERENCE %s', $referenceInModel->getName());
                }
            }
        }

    }

    /**
     * Get columns from database.
     *
     * @return array
     */
    protected function _getColumnsFromDatabase()
    {
        $result = [];
        $columns = $this->_db->describeColumns($this->getTableName(), $this->getSchemaName());
        foreach ($columns as $column) {
            $result[$column->getName()] = $column;
        }

        return $result;
    }

    /**
     * Get columns from model.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function _getColumnsFromModel()
    {
        if (empty($this->_metadata['columns'])) {
            throw new \Exception('Table must have at least one column');
        }

        $result = [];
        foreach ($this->_metadata['columns'] as $column) {
            if (!is_object($column)) {
                throw new \Exception('Wrong column definition, it must be a object');
            }
            $result[$column->getName()] = $column;
        }

        return $result;
    }

    /**
     * Get indexes from database.
     *
     * @return array
     */
    protected function _getIndexesFromDatabase()
    {
        $result = [];
        $indexes = $this->_db->describeIndexes($this->getTableName(), $this->getSchemaName());
        foreach ($indexes as $index) {
            $result[$index->getName()] = $index->getColumns();
        }

        return $result;
    }

    /**
     * Get indexes from model.
     *
     * @return array
     */
    protected function _getIndexesFromModel()
    {
        if (empty($this->_metadata['indexes'])) {
            return [];
        }

        $result = [];
        $indexes = $this->_metadata['indexes'];
        foreach ($indexes as $index) {
            $result[$index->getName()] = $index;
        }

        return $result;
    }

    /**
     * Get references from database.
     *
     * @return array
     */
    protected function _getReferencesFromDatabase()
    {
        $result = [];
        $references = $this->_db->describeReferences($this->getTableName(), $this->getSchemaName());
        foreach ($references as $ref) {
            $result[$ref->getName()] = [
                'referencedTable' => $ref->getReferencedTable(),
                'columns' => array_unique($ref->getColumns()),
                'referencedColumns' => array_unique($ref->getReferencedColumns()),
            ];
        }

        return $result;
    }

    /**
     * Model references.
     *
     * @return mixed
     */
    protected function _getReferencesFromModel()
    {
        $result = [];
        $references = $this->_metadata['references'];
        foreach ($references as $reference) {
            $result[$reference->getName()] = $reference;
        }

        return $result;
    }

    /**
     * Check if current reference has changes.
     *
     * @param Reference $referenceInModel    Table reference.
     * @param array     $referenceInDatabase Local references.
     *
     * @return bool
     */
    protected function _checkReferenceChanges($referenceInModel, $referenceInDatabase)
    {
        if (
            $referenceInModel->getReferencedTable() !=
            $referenceInDatabase[$referenceInModel->getName()]['referencedTable']
        ) {
            return true;
        }


        if (
            count($referenceInModel->getColumns()) !=
            count($referenceInDatabase[$referenceInModel->getName()]['columns'])
        ) {
            return true;
        }


        if (
            count($referenceInModel->getReferencedColumns()) !=
            count($referenceInDatabase[$referenceInModel->getName()]['referencedColumns'])
        ) {
            return true;
        }


        foreach ($referenceInModel->getColumns() as $columnName) {
            if (!in_array($columnName, $referenceInDatabase[$referenceInModel->getName()]['columns'])) {
                return true;
                break;
            }
        }


        foreach ($referenceInModel->getReferencedColumns() as $columnName) {
            if (
            !in_array($columnName, $referenceInDatabase[$referenceInModel->getName()]['referencedColumns'])
            ) {
                return true;
                break;
            }
        }

        return false;
    }

    /**
     * Get data from object.
     *
     * @param mixed $object Object to get data from it.
     *
     * @return array Data as array.
     */
    protected function _getData($object)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[str_replace('_', '', $property->getName())] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }

    /**
     * Check if scale is allowed for column. Phalcon's column object always has scale 0 by default,
     * even for strings.
     *
     * @param Column $column $column object.
     *
     * @return bool Scale is allowed.
     */
    protected function _isColumnScaleAllowed(Column $column)
    {
        switch ($column->getType()) {
            case Column::TYPE_INTEGER:
            case Column::TYPE_FLOAT:
            case Column::TYPE_DECIMAL:
            case Column::TYPE_DOUBLE:
            case Column::TYPE_BIGINTEGER:
                return true;
        }

        return false;
    }

    /**
     * Log message.
     *
     * @param  string $message Message to log.
     * @param array   ...$args Message template arguments.
     */
    protected function _log($message, ...$args)
    {
        $this->getLogger()->info(' ------------------> ' . vsprintf($message, $args));
    }
}