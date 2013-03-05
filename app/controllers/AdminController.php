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
        $activeItem = "index";

        $navigation = new Navigation();
        $navigation
            ->setItems(array(
            'index' => array(
                'href' => 'admin',
                'title' => 'Dashboard'
            ),
            'users' => array(
                'title' => 'Manage',
                'items' => array( // type - dropdown
                    'admin/users' => 'Users',
                    'admin/pages' => 'Pages',
                    'admin/menus' => 'Menus',
                    'admin/languages' => 'Languages',
                )
            ),
            'settings' => array( // type - dropdown
                'title' => 'Settings',
                'items' => array(
                    1 => 'Main settings',
                    'admin/settings' => 'System',
                    'admin/settings/performance' => 'Performance',
//                    2 => 'divider',
//                    3 => 'Other settings',
//                    'admin/3' => 'Menu item 3',
//                    'admin/4' => 'Menu item 4',
                )
            )))
            ->setActiveItem($activeItem);

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
    public function modeAction(){
        $this->view->disable();

        $this->config->application->debug = (bool)$this->request->get('debug', null, true);
        $configText = var_export($this->config->toArray(), true);
        $configText = str_replace("'".ROOT_PATH, "ROOT_PATH . '", $configText);
        file_put_contents(ROOT_PATH . '/app/config/config.php', "<?php ".PHP_EOL.PHP_EOL."return new \\Phalcon\\Config(" . $configText .");");
    }
}

