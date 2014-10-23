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

namespace Engine\Grid\Source;

use Engine\Behaviour\DIBehaviour;
use Engine\Grid\AbstractGrid;
use Engine\Grid\Exception;
use Phalcon\DI;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Adapter\QueryBuilder;
use Phalcon\Paginator\AdapterInterface;

/**
 * Grid array behaviour.
 * Additional method for array filtering and sorting.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class BuilderResolver extends AbstractResolver
{
    /**
     * Resolve source and return paginator.
     *
     * @param mixed $source Source.
     *
     * @throws \Engine\Grid\Exception
     * @return AdapterInterface
     */
    public function resolve($source)
    {
        if (!$source instanceof Builder) {
            throw new Exception('Grid source must be instance of Phalcon\Mvc\Model\Query\Builder.');
        }

        $this->_applyFilter($source);
        $this->_applySorting($source);

        /**
         * Paginator.
         */
        return new QueryBuilder(
            [
                "builder" => $source,
                "limit" => $this->_grid->getItemsCountPerPage(),
                "page" => $this->_grid->getDI()->getRequest()->getQuery('page', 'int', 1)
            ]
        );
    }

    /**
     * Apply filter data on array.
     *
     * @param Builder $source Data.
     *
     * @return array
     */
    protected function _applyFilter(Builder $source)
    {
        $data = $this->_getParam('filter');
        foreach ($this->_grid->getColumns() as $name => $column) {
            // Can't use empty(), coz value can be '0'.
            if (!isset($data[$name]) || $data[$name] == '') {
                continue;
            }

            $conditionLike = !isset($column[AbstractGrid::COLUMN_PARAM_USE_LIKE]) ||
                $column[AbstractGrid::COLUMN_PARAM_USE_LIKE];
            if (!empty($column[AbstractGrid::COLUMN_PARAM_USE_HAVING])) {
                if ($conditionLike) {
                    $value = '%' . $data[$name] . '%';
                } else {
                    $value = $data[$name];
                }
                if (isset($column[AbstractGrid::COLUMN_PARAM_TYPE])) {
                    $value = $this->_grid->getDI()
                        ->getDb()
                        ->getInternalHandler()
                        ->quote($value, $column[AbstractGrid::COLUMN_PARAM_TYPE]);
                }
                if ($conditionLike) {
                    $source->having($name . ' LIKE ' . $value);
                } else {
                    $source->having($name . ' = ' . $value);
                }
            } else {
                $bindType = null;
                $alias = str_replace('.', '_', $name);
                if (isset($column[AbstractGrid::COLUMN_PARAM_TYPE])) {
                    $bindType = [$alias => $column[AbstractGrid::COLUMN_PARAM_TYPE]];
                }
                if ($conditionLike) {
                    $source->where($name . ' LIKE :' . $alias . ':', [$alias => '%' . $data[$name] . '%'], $bindType);
                } else {
                    $source->where($name . ' = :' . $alias . ':', [$alias => $data[$name]], $bindType);
                }
            }
        }
    }

    /**
     * Apply sorting data on array.
     *
     * @param Builder $source Data.
     *
     * @return array|void
     */
    protected function _applySorting(Builder $source)
    {
        $sort = $this->_getParam('sort');
        $direction = $this->_getParam('direction', 'DESC');

        // Additional checks.
        if (!$sort || ($direction != 'DESC' && $direction != 'ASC')) {
            return;
        }

        $source->orderBy(sprintf('%s %s', $sort, $direction));
    }
}