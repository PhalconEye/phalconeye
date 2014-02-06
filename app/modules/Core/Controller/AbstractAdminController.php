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

namespace Core\Controller;

use Core\Model\Package;
use Engine\Navigation;
use Engine\Package\Manager;

/**
 * Base admin controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractAdminController extends AbstractController
{
    /**
     * Initialize admin specific logic.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $path = explode('/', $this->request->get('_url'));

        $activeItem = '';
        $limit = (count($path) > 3 ? 1 : 0);
        for ($i = 1, $count = count($path); $i < $count - $limit; $i++) {
            $activeItem .= $path[$i] . '/';
        }
        $activeItem = substr($activeItem, 0, -1);

        $menuItems = [
            'admin' => [
                'href' => 'admin',
                'title' => 'Dashboard',
                'prepend' => '<i class="icon-home icon-white"></i>'
            ],
            'users' => [
                'title' => 'Manage',
                'items' => [ // type - dropdown
                    'admin/users' => [
                        'title' => 'Users and Roles',
                        'href' => 'admin/users',
                        'prepend' => '<i class="icon-user icon-white"></i>'
                    ],
                    'admin/pages' => [
                        'title' => 'Pages',
                        'href' => 'admin/pages',
                        'prepend' => '<i class="icon-list-alt icon-white"></i>'
                    ],
                    'admin/menus' => [
                        'title' => 'Menus',
                        'href' => 'admin/menus',
                        'prepend' => '<i class="icon-th-list icon-white"></i>'
                    ],
                    'admin/languages' => [
                        'title' => 'Languages',
                        'href' => 'admin/languages',
                        'prepend' => '<i class="icon-globe icon-white"></i>'
                    ],
                    'admin/files' => [
                        'title' => 'Files',
                        'href' => 'admin/files',
                        'prepend' => '<i class="icon-file icon-white"></i>'
                    ],
                    'admin/packages' => [
                        'title' => 'Packages',
                        'href' => 'admin/packages',
                        'prepend' => '<i class="icon-th icon-white"></i>'
                    ]
                ]
            ],
            'settings' => [ // type - dropdown
                'title' => 'Settings',
                'items' => [
                    'admin/settings' => [
                        'title' => 'System',
                        'href' => 'admin/settings',
                        'prepend' => '<i class="icon-cog icon-white"></i>'
                    ],
                    'admin/settings/performance' => [
                        'title' => 'Performance',
                        'href' => 'admin/performance',
                        'prepend' => '<i class="icon-signal icon-white"></i>'
                    ],
                    'admin/access' => [
                        'title' => 'Access Rights',
                        'href' => 'admin/access',
                        'prepend' => '<i class="icon-lock icon-white"></i>'
                    ]
                ]
            ]
        ];

        $modules = Package::findByType(Manager::PACKAGE_TYPE_MODULE, 1);
        if ($modules->count()) {
            $modulesMenuItems = [];
            foreach ($modules as $module) {
                if ($module->is_system) {
                    continue;
                }
                $href = 'admin/module/' . $module->name;
                $modulesMenuItems[$href] = [
                    'title' => $module->title,
                    'href' => $href,
                    'prepend' => '<i class="icon-th-large icon-white"></i>'
                ];
            }

            if (!empty($modulesMenuItems)) {
                $menuItems['modules'] = [
                    'title' => 'Modules',
                    'items' => $modulesMenuItems
                ];
            }
        }

        $navigation = new Navigation();
        $navigation
            ->setItems($menuItems)
            ->setActiveItem($activeItem)
            ->setListClass('nav nav-categories')
            ->setDropDownItemClass('nav-category')
            ->setDropDownItemMenuClass('nav')
            ->setDropDownIcon('')
            ->setEnabledDropDownHighlight(false);

        $this->view->headerNavigation = $navigation;

        // Assets setup.
        $this->assets->set(
            'css',
            $this->assets->getEmptyCssCollection()
                ->addCss('external/bootstrap/bootstrap.min.css')
                ->addCss('external/bootstrap/bootstrap.min.css')
                ->addCss('external/jquery/jquery-ui.css')
                ->addCss('assets/css/core/admin/main.css')
                ->join(false)
        );

        $this->assets->get('js')
            ->addJs('external/bootstrap/bootstrap.min.js')
            ->addJs('external/ckeditor/ckeditor.js');
    }
}

