<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine\Model;

use Phalcon\Mvc\ModelInterface,
    Phalcon\DiInterface,
    Phalcon\Mvc\Model\MetaData,
    Phalcon\Db\Column;

class AnnotationsMetaDataInitializer
{

    /**
     * Initializes the model's meta-data
     *
     * @param \Phalcon\Mvc\ModelInterface $model
     * @param \Phalcon\DiInterface        $di
     *
     * @return array
     */
    public function getMetaData(ModelInterface $model, DiInterface $di)
    {
        $reflection = $di['annotations']->get($model);
        $properties = $reflection->getPropertiesAnnotations();

        $attributes = array();
        $nullables = array();
        $dataTypes = array();
        $dataTypesBind = array();
        $numericTypes = array();
        $primaryKeys = array();
        $nonPrimaryKeys = array();
        $identity = null;

        foreach ($properties as $name => $collection) {

            if ($collection->has('Column')) {

                $arguments = $collection->get('Column')->getArguments();

                /**
                 * Get the column's name
                 */
                if (isset($arguments['column'])) {
                    $columnName = $arguments['column'];
                } else {
                    $columnName = $name;
                }

                /**
                 * Check for the 'type' parameter in the 'Column' annotation
                 */
                if (isset($arguments['type'])) {
                    switch ($arguments['type']) {
                        case 'integer':
                            $dataTypes[$columnName] = Column::TYPE_INTEGER;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_INT;
                            $numericTypes[$columnName] = true;
                            break;
                        case 'string':
                            $dataTypes[$columnName] = Column::TYPE_VARCHAR;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'text':
                            $dataTypes[$columnName] = Column::TYPE_TEXT;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'boolean':
                            $dataTypes[$columnName] = Column::TYPE_BOOLEAN;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_BOOL;
                            break;
                        case 'date':
                            $dataTypes[$columnName] = Column::TYPE_DATE;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'datetime':
                            $dataTypes[$columnName] = Column::TYPE_DATETIME;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                    }
                } else {
                    $dataTypes[$columnName] = Column::TYPE_VARCHAR;
                    $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                }

                /**
                 * Check for the 'nullable' parameter in the 'Column' annotation
                 */
                if (!$collection->has('Identity')) {
                    if (isset($arguments['nullable'])) {
                        if (!$arguments['nullable']) {
                            $nullables[] = $columnName;
                        }
                    }
                }

                $attributes[] = $columnName;

                /**
                 * Check if the attribute is marked as primary
                 */
                if ($collection->has('Primary')) {
                    $primaryKeys[] = $columnName;
                } else {
                    $nonPrimaryKeys[] = $columnName;
                }

                /**
                 * Check if the attribute is marked as identity
                 */
                if ($collection->has('Identity')) {
                    $identity = $columnName;
                }

            }


        }

        return array(

            //Every column in the mapped table
            MetaData::MODELS_ATTRIBUTES => $attributes,

            //Every column part of the primary key
            MetaData::MODELS_PRIMARY_KEY => $primaryKeys,

            //Every column that isn't part of the primary key
            MetaData::MODELS_NON_PRIMARY_KEY => $nonPrimaryKeys,

            //Every column that doesn't allows null values
            MetaData::MODELS_NOT_NULL => $nullables,

            //Every column and their data types
            MetaData::MODELS_DATA_TYPES => $dataTypes,

            //The columns that have numeric data types
            MetaData::MODELS_DATA_TYPES_NUMERIC => $numericTypes,

            //The identity column, use boolean false if the model doesn't have
            //an identity column
            MetaData::MODELS_IDENTITY_COLUMN => $identity,

            //How every column must be bound/casted
            MetaData::MODELS_DATA_TYPES_BIND => $dataTypesBind,

            //Fields that must be ignored from INSERT SQL statements
            MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT => array(),

            //Fields that must be ignored from UPDATE SQL statements
            MetaData::MODELS_AUTOMATIC_DEFAULT_UPDATE => array()

        );
    }

    /**
     * Initializes the model's column map
     *
     * @param \Phalcon\Mvc\ModelInterface $model
     * @param \Phalcon\DiInterface        $di
     *
     * @return array
     */
    public function getColumnMaps(ModelInterface $model, DiInterface $di)
    {
        $reflection = $di['annotations']->get($model);

        $columnMap = array();
        $reverseColumnMap = array();

        $renamed = false;
        foreach ($reflection->getPropertiesAnnotations() as $name => $collection) {

            if ($collection->has('Column')) {

                $arguments = $collection->get('Column')->getArguments();

                /**
                 * Get the column's name
                 */
                if (isset($arguments['column'])) {
                    $columnName = $arguments['column'];
                } else {
                    $columnName = $name;
                }

                $columnMap[$columnName] = $name;
                $reverseColumnMap[$name] = $columnName;

                if (!$renamed) {
                    if ($columnName != $name) {
                        $renamed = true;
                    }
                }
            }
        }

        if ($renamed) {
            return array(
                MetaData::MODELS_COLUMN_MAP => $columnMap,
                MetaData::MODELS_REVERSE_COLUMN_MAP => $reverseColumnMap
            );
        }

        return null;
    }

    /**
     * Get metadata from all models in modules.
     *
     * @param DiInterface $di Dependency Injection.
     *
     * @return array
     */
    public function getAllModelsMetadata(DiInterface $di)
    {
        $models = array();
        foreach ($di->get('modules') as $module => $enabled) {
            if (!$enabled) {
                continue;
            }
            $modelsDirectory = $di->get('config')->application->modulesDir . ucfirst($module) . '/Model';
            foreach (glob($modelsDirectory . '/*.php') as $modelPath) {
                $modelInfo = array();
                $modelClass = '\\' . ucfirst($module) . '\Model\\' . basename(str_replace('.php', '', $modelPath));
                $reflector = $di->get('annotations')->get($modelClass);

                // Get table name.
                $annotations = $reflector->getClassAnnotations();
                if ($annotations) {
                    foreach ($annotations as $annotation) {
                        if ($annotation->getName() == 'Source') {
                            $arguments = $annotation->getArguments();
                            $modelInfo['name'] = $arguments[0];
                        }
                    }
                }

                // Get table fields properties.
                $modelInfo['columns'] = array();

                $properties = $reflector->getPropertiesAnnotations();
                foreach ($properties as $name => $collection) {
                    if ($collection->has('Column')) {
                        $arguments = $collection->get('Column')->getArguments();
                        /**
                         * Get the column's name
                         */
                        if (isset($arguments['column'])) {
                            $columnName = $arguments['column'];
                        } else {
                            $columnName = $name;
                        }
                        $modelInfo['columns'][$columnName] = array();

                        /**
                         * Get type.
                         */
                        if (isset($arguments['type'])) {
                            switch ($arguments['type']) {
                                case 'integer':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_INTEGER;
                                    $modelInfo['columns'][$columnName]['is_numeric'] = true;
                                    break;
                                case 'string':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_VARCHAR;
                                    break;
                                case 'text':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_TEXT;
                                    break;
                                case 'boolean':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_BOOLEAN;
                                    break;
                                case 'date':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_DATE;
                                    break;
                                case 'datetime':
                                    $modelInfo['columns'][$columnName]['type'] = Column::TYPE_DATETIME;
                                    break;
                            }
                        }

                        /**
                         * Get size.
                         */
                        if (isset($arguments['size'])) {
                            $modelInfo['columns'][$columnName]['size'] = $arguments['size'];
                        }

                        /**
                         * Check for the 'nullable' parameter in the 'Column' annotation.
                         */
                        if (!$collection->has('Identity')) {
                            if (isset($arguments['nullable'])) {
                                $modelInfo['columns'][$columnName]['nullable'] = $arguments['nullable'];
                            }
                        }

                        /**
                         * Check if the attribute is marked as primary
                         */
                        if ($collection->has('Primary')) {
                            $modelInfo['columns'][$columnName]['is_primary'] = true;
                        }

                        /**
                         * Check if the attribute is marked as identity
                         */
                        if ($collection->has('Identity')) {
                            $modelInfo['columns'][$columnName]['identity'] = true;
                        }

                    }
                }
                $models[] = $modelInfo;
            }
        }

        return $models;

    }

}