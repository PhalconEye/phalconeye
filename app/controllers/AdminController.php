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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

/**
 * @RoutePrefix("/admin")
 */
class AdminController extends Controller
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


        $navigation = new Navigation();
        $navigation
            ->setItems(array(
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
                            'href' => 'admin/settings/performance',
                            'prepend' => '<i class="icon-signal icon-white"></i>'
                        ),
                        'admin/access' => array(
                            'title' => 'Access Rights',
                            'href' => 'admin/access',
                            'prepend' => '<i class="icon-lock icon-white"></i>'
                        )
                    )
                )))
            ->setActiveItem($activeItem)
            ->setListClass('nav nav-categories')
            ->setDropDownItemClass('nav-category')
            ->setDropDownItemMenuClass('nav')
            ->setDropDownIcon('')
            ->setEnabledDropDownHighlight(false);

        $this->view->setVar('headerNavigation', $navigation);
    }

    /**
     * @Get("/", name="admin-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->setVar('debug', $this->config->application->debug);
    }

    /**
     * @Get("/mode", name="admin-mode")
     */
    public function modeAction()
    {
        $this->view->disable();

        $this->config->application->debug = (bool)$this->request->get('debug', null, true);
        $configText = var_export($this->config->toArray(), true);
        $configText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $configText);
        file_put_contents(ROOT_PATH . '/app/config/config.php', "<?php " . PHP_EOL . PHP_EOL . "return new \\Phalcon\\Config(" . $configText . ");");
    }
}

