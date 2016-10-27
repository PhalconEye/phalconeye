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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Navigation\Backoffice;

use Core\Model\PackageModel;
use Core\Navigation\CoreNavigation;
use Engine\Navigation\Item;
use Engine\Package\Manager;

/**
 * Main Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MainNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function __construct($di = null)
    {
        $this->_options = array_merge(
            $this->_options,
            [
                'listClass' => 'nav nav-categories',
                'dropDownItemClass' => 'nav-category',
                'dropDownItemMenuClass' => 'nav'
            ]
        );

        parent::__construct($di = null);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        // todo: re-organize
        $path = explode('/', $this->_activeItem);

        $activeItem = '';
        $limit = (count($path) > 3 ? 1 : 0);
        for ($i = 1, $count = count($path); $i < $count - $limit && $i < 3; $i++) {
            $activeItem .= $path[$i] . '/';
        }

        // Dashboard
        $this->appendItem(
            new Item(
                'Dashboard',
                'backoffice',
                [
                    'prepend' => '<i class="glyphicon glyphicon-home"></i>'
                ]
            )
        );

        // Manage
        $this->appendItem($mangeItem = new Item('Manage'));

        $mangeItem->setItems(
            [
                ['Users and Roles', 'backoffice/users', [
                    'prepend' => '<i class="glyphicon glyphicon-user"></i>'
                ]],
                ['Pages', 'backoffice/pages', [
                    'prepend' => '<i class="glyphicon glyphicon-list-alt"></i>'
                ]],
                ['Menus', 'backoffice/menus', [
                    'prepend' => '<i class="glyphicon glyphicon-th-list"></i>'
                ]],
                ['Languages', 'backoffice/languages', [
                    'prepend' => '<i class="glyphicon glyphicon-globe"></i>'
                ]],
                ['Files', 'backoffice/files', [
                    'prepend' => '<i class="glyphicon glyphicon-file"></i>'
                ]],
                ['Packages', 'backoffice/packages', [
                    'prepend' => '<i class="glyphicon glyphicon-th"></i>'
                ]]
            ]
        );

        // Settings
        $this->appendItem($settingsItem = new Item('Settings'));

        $settingsItem->setItems(
            [
                ['System', 'backoffice/settings', [
                    'prepend' => '<i class="glyphicon glyphicon-cog"></i>'
                ]],
                ['Performance', 'backoffice/performance', [
                    'prepend' => '<i class="glyphicon glyphicon-signal"></i>'
                ]],
                ['Access Rights', 'backoffice/access', [
                    'prepend' => '<i class="glyphicon glyphicon-lock"></i>'
                ]]
            ]
        );

        // Dynamic modules
        // @TODO: refactor
//        $modules = PackageModel::findByType(Manager::PACKAGE_TYPE_MODULE, 1);
//        if ($modules->count()) {
//            $modulesMenuItem = null;
//            foreach ($modules as $module) {
//                if ($module->is_system) {
//                    continue;
//                }
//
//                if (!$modulesMenuItem) {
//                    $modulesMenuItem = new Item('Modules');
//                    $this->appendItem($modulesMenuItem);
//                }
//
//                $modulesMenuItem->appendItem(
//                    new Item(
//                        $module->title,
//                        'backoffice/module/' . $module->name,
//                        [
//                            'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
//                        ]
//                    )
//                );
//
//                if ($activeItem == 'backoffice/module' && (string)$path[3] == $module->name) {
//                    $this->setActiveItem('backoffice/module/' . $module->name);
//                }
//            }
//        }
    }
}
