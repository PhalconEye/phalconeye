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

use Core\Model\Package;use Engine\Package\Exception;

/**
 * @RoutePrefix("/admin/packages", name="admin-packages")
 */
class AdminPackagesController extends \Core\Controller\BaseAdmin
{
    public function init()
    {
        $navigation = new \Engine\Navigation();
        $navigation
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/packages',
                    'title' => 'Modules',
                    'prepend' => '<i class="icon-th-large icon-white"></i>'
                ),
                'themes' => array(
                    'href' => 'admin/packages/themes',
                    'title' => 'Themes',
                    'prepend' => '<i class="icon-leaf icon-white"></i>'
                ),
                'widgets' => array(
                    'href' => 'admin/packages/widgets',
                    'title' => 'Widgets',
                    'prepend' => '<i class="icon-tags icon-white"></i>'
                ),
                'plugins' => array(
                    'href' => array('for' => 'admin-packages-plugins'),
                    'title' => 'Plugins',
                    'prepend' => '<i class="icon-resize-full icon-white"></i>'
                ),
                'libraries' => array(
                    'href' => array('for' => 'admin-packages-libraries'),
                    'title' => 'Libraries',
                    'prepend' => '<i class="icon-book icon-white"></i>'
                ),
                2 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'upload' => array(
                    'href' => 'admin/packages/upload',
                    'title' => 'Upload new package',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                ),
                'create' => array(
                    'href' => 'admin/packages/create',
                    'title' => 'Create new package',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                )
            ));

        $this->view->navigation = $navigation;

    }

    /**
     * @Route("/", methods={"GET"}, name="admin-packages")
     */
    public function indexAction()
    {
        $this->view->packages = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_MODULE, null, 'enabled DESC');
    }

    /**
     * @Route("/themes", methods={"GET"}, name="admin-packages-themes")
     */
    public function themesAction()
    {
        $this->view->packages = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_THEME, null, 'enabled DESC');
    }

    /**
     * @Route("/widgets", methods={"GET"}, name="admin-packages-widgets")
     */
    public function widgetsAction()
    {
        $this->view->packages = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_WIDGET, null, 'enabled DESC');
    }

    /**
     * @Route("/plugins", methods={"GET"}, name="admin-packages-plugins")
     */
    public function pluginsAction()
    {
        $this->view->packages = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_PLUGIN, null, 'enabled DESC');
    }

    /**
     * @Route("/libraries", methods={"GET"}, name="admin-packages-libraries")
     */
    public function librariesAction()
    {
        $this->view->packages = \Core\Model\Package::findByType(\Engine\Package\Manager::PACKAGE_TYPE_LIBRARY, null, 'enabled DESC');
    }

    /**
     * @Route("/upload", methods={"GET", "POST"}, name="admin-packages-upload")
     */
    public function uploadAction()
    {
        $this->view->form = $form = new \Core\Form\Admin\Package\Upload();

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $packageManager = new \Engine\Package\Manager(Package::find());
        $packageManager->clearTempDirectory();

        $packageFile = $this->request->getUploadedFiles();
        if (count($packageFile) == 1 && $packageFile[0]->getSize() != 0) {
            $filename = $packageManager->getTempDirectory() . 'uploaded.zip';
            $packageFile[0]->moveTo($filename);
            try {
                // install package - check dep, copy files, get manifest, etc
                $manifest = $packageManager->installPackage($filename);

                // create package database object
                if (!$manifest->isUpdate) {
                    $package = new Package();
                    $package->save($manifest->toArray());
                    $this->_enablePackageConfig($package->name, $package->type, $manifest->toArray());

                    // install package dependencies
                    if ($manifest->get('dependencies')) {
                        $dependencies = $manifest->get('dependencies');
                        foreach ($dependencies as $dependecy) {
                            $needPackage = $this->_getPackage($dependecy['type'], $dependecy['name']);
                            if ($needPackage) {
                                $packageDependency = new \Core\Model\PackageDependency();
                                $packageDependency->package_id = $package->id;
                                $packageDependency->dependencyId = $needPackage->id;
                                $packageDependency->save();
                            }
                        }
                    }
                }

                // Run module install script.
                $newPackageVersion = $packageManager->runInstallScript($manifest);
                $this->app->clearCache();

                if ($manifest->isUpdate) {
                    $this->flash->success('Package updated to version ' . $newPackageVersion . '!');
                } else {
                    $this->flash->success('Package installed!');
                }

            } catch (\Engine\Package\Exception $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            $this->flash->notice('Please, select zip file...');
        }

    }

    /**
     * @Route("/create", methods={"GET", "POST"}, name="admin-packages-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new \Core\Form\Admin\Package\Create();

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $data = $form->getValues(false);
        $packageManager = new \Engine\Package\Manager();
        $packageManager->createPackage($data);
        $this->_enablePackageConfig($data['name'], $data['type']);

        $return = '';
        switch ($data['type']) {
            case \Engine\Package\Manager::PACKAGE_TYPE_MODULE:
            {
                $return = 'admin-packages';
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_THEME:
            {
                $return = 'admin-packages-themes';
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_WIDGET:
            {
                $return = 'admin-packages-widgets';
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_PLUGIN:
            {
                $return = 'admin-packages-plugins';
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY:
            {
                $return = 'admin-packages-libraries';
            }
                break;
            default:
                {
                $return = 'admin-packages';
                }
        }

        $this->flashSession->success('New package created successfully!');
        return $this->response->redirect(array('for' => $return));
    }

    /**
     * @Route("/edit/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}", methods={"GET", "POST"}, name="admin-packages-edit")
     */
    public function editAction($type, $name, $return)
    {

        $package = $this->_getPackage($type, $name);
        if (!$package) {
            return $this->response->redirect(array('for' => $return));
        }

        $this->view->form = $form = new \Core\Form\Admin\Package\Edit($package, $return);

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $this->flashSession->success('Package saved!');
        return $this->response->redirect(array('for' => $return));
    }

    /**
     * @Route("/export/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}", methods={"GET", "POST"}, name="admin-packages-export")
     */
    public function exportAction($type, $name)
    {
        $this->view->hideFooter = true;
        $this->view->form = $form = new \Core\Form\Admin\Package\Export(array('name' => $name, 'type' => $type));
        if ($type == \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY) {
            // library can have dependecy only on another library
            $form->removeElement('modules');
        }

        $skipForm = ($type == \Engine\Package\Manager::PACKAGE_TYPE_THEME);
        if (!$skipForm) {
            if (!$this->request->isPost() || !$form->isValid($_POST)) {
                return;
            }
        }

        $this->view->disable();
        $package = $this->_getPackage($type, $name);
        if ($package) {
            $dependecies = $form->getValues();
            $data = $package->toArray();

            // collect modules
            if (!empty($dependecies['modules']))
                foreach ($dependecies['modules'] as $dependecy) {
                    $package = $this->_getPackage(\Engine\Package\Manager::PACKAGE_TYPE_MODULE, $dependecy);

                    $data['dependencies'][] = array(
                        'name' => $dependecy,
                        'type' => \Engine\Package\Manager::PACKAGE_TYPE_MODULE,
                        'version' => $package->version,
                    );
                }

            // collect libraries
            if (!empty($dependecies['libraries']))
                foreach ($dependecies['libraries'] as $dependecy) {
                    $package = $this->_getPackage(\Engine\Package\Manager::PACKAGE_TYPE_LIBRARY, $dependecy);

                    $data['dependencies'][] = array(
                        'name' => $dependecy,
                        'type' => \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY,
                        'version' => $package->version,
                    );
                }

            // collect hooks
            if ($type == \Engine\Package\Manager::PACKAGE_TYPE_MODULE) {
                $moduleEvents = $this->config->events->get($name);
                if (!empty($moduleEvents)) {
                    foreach ($moduleEvents as $event) {
                        $data['events'][] = $event;
                    }
                }
            } elseif ($type == \Engine\Package\Manager::PACKAGE_TYPE_PLUGIN) {
                $pluginEvent = $this->config->plugins->get($name);
                if (isset($pluginEvent['events'])) {
                    $data['events'] = $pluginEvent['events'];
                }
            }

            // collect widgets
            if ($type == \Engine\Package\Manager::PACKAGE_TYPE_MODULE) {
                $query = $this->modelsManager->createBuilder()
                    ->from(array('t' => '\Core\Model\Widget'))
                    ->where("t.module = :module:", array('module' => $name));

                $widgets = $query->getQuery()->execute();
                foreach ($widgets as $widget) {
                    $data['widgets'][] = array(
                        'name' => $widget->name,
                        'module' => $name,
                        'description' => $widget->description,
                        'is_paginated' => $widget->is_paginated,
                        'is_acl_controlled' => $widget->is_acl_controlled,
                        'admin_form' => $widget->admin_orm,
                        'enabled' => (bool)$widget->enabled,
                    );
                }
            }


            $packageManager = new \Engine\Package\Manager();
            $packageManager->exportPackage($name, $data);
        }
    }

    /**
     * @Route("/uninstall/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}", methods={"GET"}, name="admin-packages-uninstall")
     */
    public function uninstallAction($type, $name, $return)
    {
        $this->view->disable();
        $package = $this->_getPackage($type, $name);
        if ($package) {
            if ($this->_hasDependencies($package))
                return $this->response->redirect(array('for' => $return));

            try {

                $installerClass = ucfirst($name) . '\Installer';
                if (class_exists($installerClass)) {
                    $packageInstaller = new $installerClass($this->di, $name);
                    if (method_exists($packageInstaller, 'remove')) {
                        $packageInstaller->remove();
                    }
                }

                $packageManager = new \Engine\Package\Manager();
                $packageManager->removePackage($package->name, $package->type);

                $package->delete();

                $this->_removePackageConfig($name, $package->type);
                $this->app->clearCache();
                $this->flashSession->success('Package "' . $name . '" removed!');
            } catch (Exception $e) {
                $package->delete();
                $this->flashSession->notice('Failed to remove package directory, check logs...');
            }
        } else {
            $this->flashSession->notice('Package not found...');
        }

        return $this->response->redirect(array('for' => $return));
    }

    /**
     * @Route("/enable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}", methods={"GET"}, name="admin-packages-enable")
     */
    public function enableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            $package->enabled = 1;
            $package->save();

            $this->_enablePackageConfig($name, $package->type);
            $this->app->clearCache();
        }

        return $this->response->redirect(array('for' => $return));
    }

    /**
     * @Route("/disable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}", methods={"GET"}, name="admin-packages-disable")
     */
    public function disableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            if ($this->_hasDependencies($package))
                return $this->response->redirect(array('for' => $return));

            $package->enabled = 0;
            $package->save();

            $this->_disablePackageConfig($name, $package->type);
            $this->app->clearCache();
        }

        return $this->response->redirect(array('for' => $return));
    }

    private function _getPackage($type, $name)
    {
        $query = $this->modelsManager->createBuilder()
            ->from(array('t' => '\Core\Model\Package'))
            ->where("t.type = :type: AND t.name = :name:", array('type' => $type, 'name' => $name));

        return $query->getQuery()->execute()->getFirst();
    }

    private function _removePackageConfig($name, $type)
    {
        switch ($type) {
            case \Engine\Package\Manager::PACKAGE_TYPE_MODULE:
            {
                $modules = $this->config->modules->toArray();
                unset($modules[$name]);
                $this->config->modules = new \Phalcon\Config($modules);

                $events = $this->config->events->toArray();
                unset($events[$name]);
                $this->config->events = new \Phalcon\Config($events);

                // remove widgets
                $this->db->delete(\Core\Model\Widget::getTableName(), 'module = ?', array($name));
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_THEME:
            {

            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_WIDGET:
            {
                $widget = \Core\Model\Widget::findFirstByName($name);
                if ($widget) {
                    $widget->delete();
                }
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_PLUGIN:
            {
                $plugins = $this->config->plugins->toArray();
                unset($plugins[$name]);
                $this->config->plugins = new \Phalcon\Config($plugins);
            }
                break;

        }

        $this->app->saveConfig();
    }

    private function _enablePackageConfig($name, $type, $data = null)
    {
        switch ($type) {
            case \Engine\Package\Manager::PACKAGE_TYPE_MODULE:
            {
                $modules = $this->config->modules->toArray();
                $modules[$name] = true;
                $this->config->modules = new \Phalcon\Config($modules);

                if (!empty($data['events'])) {
                    $events = $this->config->events->toArray();
                    $events[$name] = $data['events'];
                    $this->config->events = new \Phalcon\Config($events);
                }

                // install widgets
                if (!empty($data['widgets'])) {
                    $errors = array();
                    foreach ($data['widgets'] as $widgetData) {
                        try {
                            $widget = new \Core\Model\Widget();
                            $widget->save($widgetData);
                        } catch (\PDOException $e) {
                            $this->flash->notice('Failed to install module widget... Check logs.');
                            \Engine\Error::exception($e);
                        }
                        if ($widget->validationHasFailed()) {
                            $messages = $widget->getMessages();
                            foreach ($messages as $message) {
                                $errors[] = $message->getMessage();
                            }
                        }
                    }

                    if (!empty($errors)) {
                        $this->flash->notice('There was some errors during installation:' . implode('<br/> - ', $errors));
                    }
                }

                // enable module widgets
                $this->db->update(\Core\Model\Widget::getTableName(), array('enabled'), array(1), "module = '{$name}'");
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_THEME:
            {

            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_WIDGET:
            {
                $widget = \Core\Model\Widget::findFirstByName($name);
                if ($widget) {
                    $widget->enabled = 1;
                    $widget->save();
                } else {
                    $widget = new \Core\Model\Widget();
                    $package = $this->_getPackage($type, $name);
                    $data = $package->toArray();
                    $data['name'] = ucfirst($name);
                    $widget->save($data);
                }
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_PLUGIN:
            {
                $plugins = $this->config->plugins->toArray();
                if (empty($plugins[$name])) {
                    if (!empty($data['events'])) {
                        $plugins[$name] = array(
                            'enabled' => true,
                            'events' => $data['events']
                        );
                    } else {
                        $plugins[$name] = array(
                            'enabled' => true,
                            'events' => ''
                        );
                    }
                } else {
                    $plugins[$name]['enabled'] = true;
                }
                $this->config->plugins = new \Phalcon\Config($plugins);
            }
                break;

        }

        $this->app->saveConfig();
    }

    private function _disablePackageConfig($name, $type)
    {
        switch ($type) {
            case \Engine\Package\Manager::PACKAGE_TYPE_MODULE:
            {
                $modules = $this->config->modules->toArray();
                $modules[$name] = false;
                $this->config->modules = new \Phalcon\Config($modules);

                // disable module widgets
                $this->db->update(\Core\Model\Widget::getTableName(), array('enabled'), array(0), "module = '{$name}'");
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_THEME:
            {

            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_WIDGET:
            {
                $widget = \Core\Model\Widget::findFirstByName($name);
                if ($widget) {
                    $widget->enabled = 0;
                    $widget->save();
                }
            }
                break;
            case \Engine\Package\Manager::PACKAGE_TYPE_PLUGIN:
            {
                $plugins = $this->config->plugins->toArray();
                if (empty($plugins[$name])) {
                    $plugins[$name] = array(
                        'enabled' => false,
                        'events' => ''
                    );
                } else {
                    $plugins[$name]['enabled'] = false;
                }
                $this->config->plugins = new \Phalcon\Config($plugins);
            }
                break;

        }

        $this->app->saveConfig();
    }

    private function _hasDependencies(\Core\Model\Package $package)
    {
        $dependencies = $package->getRelatedPackages();
        /** @var \Phalcon\Mvc\Model\Resultset\Simple $dependencies */
        if ($dependencies->count()) {
            $message = 'You can\'t uninstall or disable this package, because it\'s related to:<br/>';
            foreach ($dependencies as $dependency) {
                $dependencyPackage = $dependency->getDependencyPackage();
                $message .= " - {$dependencyPackage->type} '{$dependencyPackage->name}'";
            }
            $this->flashSession->error($message);
            return true;
        }

        return false;
    }

}

