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

class AdminIndexController extends Controller
{
    public function init()
    {
        $viewer = User::getViewer();
        if ($this->acl->_()->isAllowed($viewer->getRole()->getName(), Api_Acl::ACL_ADMIN_AREA, 'access') != \Phalcon\Acl::ALLOW){
            return  $this->dispatcher->forward(array(
                "controller" => 'error',
                "action" => 'show404'
            ));
        }

        // dispatch admin routes
        $controller = $this->dispatcher->getParam('admin_controller');
        $action = $this->dispatcher->getParam('admin_action');
        $admin_id = $this->dispatcher->getParam('admin_id');
        if (!is_array($admin_id))
            $admin_id = array($admin_id);

        if (!$action){
            $action = 'index';
        }
        else{
            // split action
            if (strpos($action, '-') != -1){
                $actionPath = explode('-', $action);
                $flag = false;
                $action = '';
                foreach($actionPath as $path){
                    if ($flag){
                        $action .= ucfirst($path);
                    }
                    else{
                        $action .= $path;
                        $flag = true;
                    }
                }

            }
        }

        if ($controller && $action) {
            $this->dispatcher->forward(array(
                "controller" => 'admin-' . $controller,
                "action" => $action,
                "params" => $admin_id
            ));
        }

        if ($controller == null){
            $activeItem = "index";
        }
        else{
            $activeItem = "admin/{$controller}";
        }

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
                    'admin/settings/index' => 'System',
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

    public function indexAction()
    {
        $this->view->setVar('debug', $this->config->application->debug);
    }

    public function modeAction(){
        $this->view->disable();

        $this->config->application->debug = (bool)$this->request->get('debug', null, true);
        $configText = var_export($this->config->toArray(), true);
        $configText = str_replace("'".ROOT_PATH, "ROOT_PATH . '", $configText);
        file_put_contents(ROOT_PATH . '/app/config/config.php', "<?php ".PHP_EOL.PHP_EOL."return new \\Phalcon\\Config(" . $configText .");");
    }
}

