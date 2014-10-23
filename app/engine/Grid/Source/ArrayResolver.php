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
use Engine\Grid\Exception;
use Phalcon\DI;
use Phalcon\Paginator\Adapter\NativeArray;
use Phalcon\Paginator\AdapterInterface;

/**
 * Array source resolver.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ArrayResolver extends AbstractResolver
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
        if (!is_array($source)) {
            throw new Exception('Grid source must be instance of array.');
        }

        $source = $this->_applyFilter($source);
        $source = $this->_applySorting($source);

        /**
         * Paginator.
         */
        return new NativeArray(
            [
                "data" => $source,
                "limit" => $this->_grid->getItemsCountPerPage(),
                "page" => $this->_grid->getDI()->getRequest()->getQuery('page', 'int', 1)
            ]
        );
    }

    /**
     * Apply filter data on array.
     *
     * @param array $source Data.
     *
     * @return array
     */
    protected function _applyFilter(array $source)
    {
        $result = [];
        $filterData = $this->_getParam('filter');

        if (empty($filterData)) {
            return $source;
        }

        foreach ($filterData as $key => $value) {
            foreach ($source as $item) {
                if (
                    strpos($item[$key], $value) !== false
                ) {
                    $result[$key] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Apply sorting data on array.
     *
     * @param array $data Data.
     *
     * @return array
     */
    protected function _applySorting(array $data)
    {
        $sort = $this->_getParam('sort');
        $direction = $this->_getParam('direction', null, 'DESC');

        // Additional checks.
        if (!$sort || ($direction != 'DESC' && $direction != 'ASC')) {
            return $data;
        }

        usort(
            $data,
            function ($a, $b) use ($sort, $direction) {
                if ($direction == 'ASC') {
                    return $a[$sort] > $b[$sort];
                } else {
                    return $a[$sort] < $b[$sort];
                }
            }
        );

        return $data;
    }
}