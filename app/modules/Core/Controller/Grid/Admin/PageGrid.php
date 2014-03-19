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

namespace Core\Controller\Grid\Admin;

use Core\Controller\Grid\CoreGrid;
use Core\Model\Page;
use Engine\Form;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\View;

/**
 * Page grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PageGrid extends CoreGrid
{
    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder->from('Core\Model\Page');

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
        $actions = [
            'Manage' => ['href' => ['for' => 'admin-pages-manage', 'id' => $item['id']]]
        ];

        if (empty($item['type'])) {
            $actions['Edit'] = ['href' => ['for' => 'admin-pages-edit', 'id' => $item['id']]];

            $actions['Delete'] = [
                'href' => ['for' => 'admin-pages-delete', 'id' => $item['id']],
                'attr' => ['class' => 'grid-action-delete']
            ];
        } elseif ($item['type'] == Page::PAGE_TYPE_HOME) {
            $actions['Edit'] = ['href' => ['for' => 'admin-pages-edit', 'id' => $item['id']]];
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
            ->addTextColumn('title', 'Title')
            ->addTextColumn('url', 'Url')
            ->addTextColumn(
                'layout',
                'Layout',
                [
                    self::COLUMN_PARAM_FILTER => false,
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function (GridItem $item, $di) {
                            $url = $di->get('url')->get($item->getObject()->getLayoutIcon());
                            return sprintf('<img alt="" src="%s"/>', $url);
                        }
                ]
            )
            ->addTextColumn('controller', 'Controller');
    }
}