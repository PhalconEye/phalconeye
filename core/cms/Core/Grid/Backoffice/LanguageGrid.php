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
use Engine\Config;
use Engine\Grid\GridItem;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Language grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class LanguageGrid extends CoreGrid
{
    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder->from('Core\Model\LanguageModel');

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
            'Manage' => ['href' => ['for' => 'backoffice-languages-manage', 'id' => $item['id']]],
            'Export' => [
                'href' => ['for' => 'backoffice-languages-export', 'id' => $item['id']],
                'attr' => ['data-widget' => 'modal']
            ],
            'Wizard' => [
                'href' => ['for' => 'backoffice-languages-wizard', 'id' => $item['id']],
                'attr' => ['data-widget' => 'modal']
            ],
            '|' => [],
            'Edit' => ['href' => ['for' => 'backoffice-languages-edit', 'id' => $item['id']]],
            'Delete' => [
                'href' => [
                    'for' => 'backoffice-languages-delete', 'id' => $item['id']
                ],
                'attr' => ['class' => 'grid-action-delete']
            ]
        ];

        if (
            $item->getObject()->language == Config::CONFIG_DEFAULT_LANGUAGE &&
            $item->getObject()->locale == Config::CONFIG_DEFAULT_LOCALE
        ) {
            unset($actions['|']);
            unset($actions['Edit']);
            unset($actions['Wizard']);
            unset($actions['Delete']);
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
            ->addTextColumn('name', 'Name')
            ->addTextColumn('language', 'Language')
            ->addTextColumn('locale', 'Locale')
            ->addTextColumn(
                'icon',
                'Icon',
                [
                    self::COLUMN_PARAM_FILTER => false,
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function (GridItem $item, $di) {
                            if (empty($item['icon'])) {
                                return $di->get('i18n')->_('No icon');
                            }

                            return sprintf('<img alt="" src="%s"/>', $item->getObject()->getIcon());
                        }
                ]
            );
    }
}