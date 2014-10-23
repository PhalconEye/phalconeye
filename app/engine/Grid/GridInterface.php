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

namespace Engine\Grid;

use Engine\Db\AbstractModel;
use Engine\Form;
use Engine\Grid\Source\ResolverInterface;
use Phalcon\Http\ResponseInterface;

/**
 * Grid interface.
 *
 * @category  PhalconEye
 * @package   Engine\Grid
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface GridInterface
{
    /**
     * Get grid identity.
     *
     * @return string
     */
    public function getId();

    /**
     * Get grid view name.
     *
     * @return string
     */
    public function getLayoutView();

    /**
     * Get grid item view name.
     *
     * @return string
     */
    public function getItemView();

    /**
     * Get grid table body view name.
     *
     * @return string
     */
    public function getTableBodyView();

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item);

    /**
     * Returns response object if grid has something to say =)... (has it's own response).
     *
     * @return null|ResponseInterface
     */
    public function getResponse();

    /**
     * Grid has actions?
     *
     * @return bool
     */
    public function hasActions();

    /**
     * Grid has filter form?
     *
     * @return bool
     */
    public function hasFilterForm();

    /**
     * Get current grid items.
     *
     * @return AbstractModel[]
     */
    public function getItems();

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Get router name.
     *
     * @return string
     */
    public function getRoute();

    /**
     * Get main data source.
     * It can be array or query builder.
     *
     * @return mixed
     */
    public function getSource();

    /**
     * Get source resolver
     *
     * @return ResolverInterface
     */
    public function getSourceResolver();

    /**
     * Get items count per page.
     *
     * @return int
     */
    public function getItemsCountPerPage();

    /**
     * Get total items count.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Render grid.
     *
     * @return string
     */
    public function render();
}