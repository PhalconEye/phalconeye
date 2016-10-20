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

namespace User\Grid\Backoffice;

use Core\Grid\CoreGrid;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Role grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RoleGrid extends CoreGrid
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
            ->from('User\Model\RoleModel');

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
        $actions = ['Edit' => ['href' => ['for' => 'backoffice-roles-edit', 'id' => $item['id']]]];
        if (empty($item['undeletable'])) {
            $actions['Delete'] = [
                'href' => ['for' => 'backoffice-roles-delete', 'id' => $item['id']],
                'attr' => ['class' => 'grid-action-delete']
            ];
        }

        return $actions;
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
            ->addTextColumn('name', 'name')
            ->addTextColumn('description', 'Description')
            ->addTextColumn('is_default', 'Is default?');
    }
}