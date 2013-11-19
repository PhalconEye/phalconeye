<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Core\Controller;

class BaseAdmin extends Base
{
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

        $menuItems = array(
            'admin' => array(
                'href' => 'admin',
                'title' => 'Dashboard',
                'prepend' => '<i class="icon-home icon-white"></i>'
            ),
            'users' => array(
                'title' => 'Manage',
                'items' => array( // type - dropdown
                    'admin/users' => array(
                        'title' => 'Users and Roles',
                        'href' => 'admin/users',
                        'prepend' => '<i class="icon-user icon-white"></i>'
                    ),
                    'admin/pages' => array(
                        'title' => 'Pages',
                        'href' => 'admin/pages',
                        'prepend' => '<i class="icon-list-alt icon-white"></i>'
                    ),
                    'admin/menus' => array(
                        'title' => 'Menus',
                        'href' => 'admin/menus',
                        'prepend' => '<i class="icon-th-list icon-white"></i>'
                    ),
                    'admin/languages' => array(
                        'title' => 'Languages',
                        'href' => 'admin/languages',
                        'prepend' => '<i class="icon-globe icon-white"></i>'
                    ),
                    'admin/files' => array(
                        'title' => 'Files',
                        'href' => 'admin/files',
                        'prepend' => '<i class="icon-file icon-white"></i>'
                    ),
                    'admin/packages' => array(
                        'title' => 'Packages',
                        'href' => 'admin/packages',
                        'prepend' => '<i class="icon-th icon-white"></i>'
                    )
                )
            ),
            'settings' => array( // type - dropdown
                'title' => 'Settings',
                'items' => array(
                    'admin/settings' => array(
                        'title' => 'System',
                        'href' => 'admin/settings',
                        'prepend' => '<i class="icon-cog icon-white"></i>'
                    ),
                    'admin/settings/performance' => array(
                        'title' => 'Performance',
                        'href' => 'admin/performance',
                        'prepend' => '<i class="icon-signal icon-white"></i>'
                    ),
                    'admin/access' => array(
                        'title' => 'Access Rights',
                        'href' => 'admin/access',
                        'prepend' => '<i class="icon-lock icon-white"></i>'
                    )
                )
            ));

        $modules = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_MODULE, 1);
        if ($modules->count()) {
            $modulesMenuItems = array();
            foreach ($modules as $module) {
                if ($module->is_system) continue;
                $href = 'admin/module/' . $module->name;
                $modulesMenuItems[$href] = array(
                    'title' => $module->title,
                    'href' => $href,
                    'prepend' => '<i class="icon-th-large icon-white"></i>'
                );
            }

            if (!empty($modulesMenuItems)) {
                $menuItems['modules'] = array(
                    'title' => 'Modules',
                    'items' => $modulesMenuItems
                );
            }
        }


        $navigation = new \Engine\Navigation();
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
                ->addCss('assets/css/core/admin/main.css')
                ->join(false)
        );

        $this->assets->get('js')
            ->addJs('external/bootstrap/bootstrap.min.js')
            ->addJs('external/ckeditor/ckeditor.js');

    }

}

