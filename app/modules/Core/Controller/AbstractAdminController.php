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
use Core\Model\Settings;
use Engine\Navigation;
use Engine\Package\Manager;
use Engine\Asset\Manager as AssetManager;

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

        if ($this->request->isAjax()) {
            return;
        }

        $this->_setupNavigation();
        $this->_setupAssets();
    }

    /**
     * Setup navigation.
     *
     * @return void
     */
    protected function _setupNavigation()
    {
        $path = explode('/', $this->request->get('_url'));

        $activeItem = '';
        $limit = (count($path) > 3 ? 1 : 0);
        for ($i = 1, $count = count($path); $i < $count - $limit && $i < 3; $i++) {
            $activeItem .= $path[$i] . '/';
        }
        $activeItem = substr($activeItem, 0, -1);

        $menuItems = [
            'admin' => [
                'href' => 'admin',
                'title' => 'Dashboard',
                'prepend' => '<i class="glyphicon glyphicon-home"></i>'
            ],
            'users' => [
                'title' => 'Manage',
                'items' => [ // type - dropdown
                    'admin/users' => [
                        'title' => 'Users and Roles',
                        'href' => 'admin/users',
                        'prepend' => '<i class="glyphicon glyphicon-user"></i>'
                    ],
                    'admin/pages' => [
                        'title' => 'Pages',
                        'href' => 'admin/pages',
                        'prepend' => '<i class="glyphicon glyphicon-list-alt"></i>'
                    ],
                    'admin/menus' => [
                        'title' => 'Menus',
                        'href' => 'admin/menus',
                        'prepend' => '<i class="glyphicon glyphicon-th-list"></i>'
                    ],
                    'admin/languages' => [
                        'title' => 'Languages',
                        'href' => 'admin/languages',
                        'prepend' => '<i class="glyphicon glyphicon-globe"></i>'
                    ],
                    'admin/files' => [
                        'title' => 'Files',
                        'href' => 'admin/files',
                        'prepend' => '<i class="glyphicon glyphicon-file"></i>'
                    ],
                    'admin/packages' => [
                        'title' => 'Packages',
                        'href' => 'admin/packages',
                        'prepend' => '<i class="glyphicon glyphicon-th"></i>'
                    ]
                ]
            ],
            'settings' => [ // type - dropdown
                'title' => 'Settings',
                'items' => [
                    'admin/settings' => [
                        'title' => 'System',
                        'href' => 'admin/settings',
                        'prepend' => '<i class="glyphicon glyphicon-cog"></i>'
                    ],
                    'admin/settings/performance' => [
                        'title' => 'Performance',
                        'href' => 'admin/performance',
                        'prepend' => '<i class="glyphicon glyphicon-signal"></i>'
                    ],
                    'admin/access' => [
                        'title' => 'Access Rights',
                        'href' => 'admin/access',
                        'prepend' => '<i class="glyphicon glyphicon-lock"></i>'
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
                    'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
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
    }

    /**
     * Setup assets files.
     *
     * @return void
     */
    protected function _setupAssets()
    {
        parent::_setupAssets();

        // Assets setup.
        $this->assets->set(
            AssetManager::DEFAULT_COLLECTION_CSS,
            $this->assets->getEmptyCssCollection()
                ->addCss('external/bootstrap/css/bootstrap.min.css')
                ->addCss('external/bootstrap/css/bootstrap-switch.min.css')
                ->addCss('external/jquery/jquery-ui.css')
                ->addCss('assets/css/core/admin/main.css')
                ->join(false)
        );

        $this->assets->get(AssetManager::DEFAULT_COLLECTION_JS)
            ->addJs('external/bootstrap/js/bootstrap.min.js')
            ->addJs('external/bootstrap/js/bootstrap-switch.min.js')
            ->addJs('external/ckeditor/ckeditor.js');
    }

    /**
     * Clear cache
     *
     * @return void
     */
    protected function _clearCache()
    {
        $this->app->clearCache(PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme'));
    }
}

