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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Grid\Backoffice;

use Core\Grid\CoreGrid;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Menu grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MenuGrid extends CoreGrid
{
    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder
            ->from('Core\Model\MenuModel')
            ->columns(['id', 'name']);

        return $builder;
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        return [
            'Manage' => ['href' => ['for' => 'backoffice-menus-manage', 'id' => $item['id']]],
            'Edit' => ['href' => ['for' => 'backoffice-menus-edit', 'id' => $item['id']]],
            'Delete' => [
                'href' =>
                    [
                        'for' => 'backoffice-menus-delete', 'id' => $item['id']
                    ],
                'attr' => ['class' => 'grid-action-delete']
            ]
        ];
    }

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    protected function _initColumns()
    {
        $this
            ->addTextColumn('id', 'ID', [self::COLUMN_PARAM_TYPE => Column::BIND_PARAM_INT])
            ->addTextColumn('name', 'Menu title');
    }
}