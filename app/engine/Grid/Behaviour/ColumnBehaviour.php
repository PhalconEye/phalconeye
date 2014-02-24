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

namespace Engine\Grid\Behaviour;

use Engine\Form\Element\Select;
use Engine\Form\Element\Text;
use Engine\Grid\AbstractGrid;
use Engine\Grid\Exception;
use Phalcon\Db\Column;
use Phalcon\Validation\Message;

/**
 * Grid columns trait.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait ColumnBehaviour
{
    /**
     * Grid columns.
     *
     * @var array
     */
    protected $_columns;

    /**
     * Get grid columns.
     *
     * @return array
     */
    public function getColumns()
    {
        if (!$this->_columns) {
            $result = $this->_initColumns();
            if (is_array($result)) {
                $this->_columns = $result;
            }
        }

        return $this->_columns;
    }

    /**
     * Set columns options.
     *
     * @param string $name   Column name.
     * @param array  $params Column $params.
     *
     * @return $this
     * @throws Exception
     */
    public function setColumnParams($name, array $params)
    {
        if (empty($this->_columns[$name])) {
            throw new Exception('Column with name "%s" not found.', [$name]);
        }

        $this->_columns[$name] = array_merge($this->_columns[$name], $params);
        return $this;
    }

    /**
     * Add column to grid.
     *
     * @param int    $id     Column id.
     * @param string $label  Column label.
     * @param array  $params Column params.
     *
     * @return $this
     */
    public function addTextColumn($id, $label, array $params = [])
    {
        $this->_columns[$id] = $this->_getDefaultColumnParams($params, $label);

        if (!empty($this->_columns[$id][AbstractGrid::COLUMN_PARAM_FILTER])) {
            $this->_columns[$id][AbstractGrid::COLUMN_PARAM_FILTER] = new Text($id);
        }

        return $this;
    }

    /**
     * Add column to grid with select filter.
     *
     * @param int    $id      Column id.
     * @param string $label   Column label.
     * @param array  $options Select options
     * @param array  $params  Column params.
     *
     * @return $this
     */
    public function addSelectColumn(
        $id,
        $label,
        array $options,
        array $params = []
    )
    {
        $this->_columns[$id] = $this->_getDefaultColumnParams($params, $label);

        if (!empty($this->_columns[$id][AbstractGrid::COLUMN_PARAM_FILTER])) {
            $element = new Select($id);
            foreach ($options as $key => $value) {
                $element->setOption($key, $value);
            }
            $this->_columns[$id][AbstractGrid::COLUMN_PARAM_FILTER] = $element;
        }

        return $this;
    }

    /**
     * Set default data to params.
     *
     * @param array  $params Columns params.
     * @param string $label  Columns label.
     *
     * @return array
     */
    protected function _getDefaultColumnParams($params, $label)
    {
        return array_merge(
            [
                AbstractGrid::COLUMN_PARAM_LABEL => $this->_($label),
                AbstractGrid::COLUMN_PARAM_TYPE => Column::BIND_PARAM_INT,
                AbstractGrid::COLUMN_PARAM_FILTER => true,
                AbstractGrid::COLUMN_PARAM_SORTABLE => true
            ],
            $params
        );
    }

    /**
     * Init grid columns, once.
     *
     * @return array
     */
    protected function _initColumns()
    {
        return [];
    }
}