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

use Core\Form\Admin\Package\Create as CreateForm;
use Core\Form\Admin\Package\Edit as EditForm;
use Core\Form\Admin\Package\Events as EventsForm;
use Core\Form\Admin\Package\Export as ExportForm;
use Core\Form\Admin\Package\Upload as UploadForm;
use Core\Form\CoreForm;
use Core\Model\Language;
use Core\Model\Package;
use Core\Model\PackageDependency;
use Core\Model\Widget;
use Engine\Db\Schema;
use Engine\Exception;
use Engine\Navigation;
use Engine\Package\Manager;
use Engine\Package\PackageException;
use Phalcon\Config;

/**
 * Admin packages controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/packages", name="admin-packages")
 */
class AdminPackagesController extends AbstractAdminController
{
    /**
     * Init controller's navigation.
     *
     * @return void
     */
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItems(
                [
                    'index' => [
                        'href' => 'admin/packages',
                        'title' => 'Modules',
                        'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
                    ],
                    'themes' => [
                        'href' => 'admin/packages/themes',
                        'title' => 'Themes',
                        'prepend' => '<i class="glyphicon glyphicon-leaf"></i>'
                    ],
                    'widgets' => [
                        'href' => 'admin/packages/widgets',
                        'title' => 'Widgets',
                        'prepend' => '<i class="glyphicon glyphicon-tags"></i>'
                    ],
                    'plugins' => [
                        'href' => ['for' => 'admin-packages-plugins'],
                        'title' => 'Plugins',
                        'prepend' => '<i class="glyphicon glyphicon-resize-full"></i>'
                    ],
                    'libraries' => [
                        'href' => ['for' => 'admin-packages-libraries'],
                        'title' => 'Libraries',
                        'prepend' => '<i class="glyphicon glyphicon-book"></i>'
                    ],
                    2 => [
                        'href' => 'javascript:;',
                        'title' => '|'
                    ],
                    'upload' => [
                        'href' => 'admin/packages/upload',
                        'title' => 'Upload new package',
                        'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                    ],
                    'create' => [
                        'href' => 'admin/packages/create',
                        'title' => 'Create new package',
                        'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                    ]
                ]
            );

        $this->view->navigation = $navigation;
    }

    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="admin-packages")
     */
    public function indexAction()
    {
        $this->view->packages = $this->_getPackages(Manager::PACKAGE_TYPE_MODULE);
    }

    /**
     * Themes actions.
     *
     * @return void
     *
     * @Route("/themes", methods={"GET"}, name="admin-packages-themes")
     */
    public function themesAction()
    {
        $this->view->packages = $this->_getPackages(Manager::PACKAGE_TYPE_THEME);
    }

    /**
     * Widgets action.
     *
     * @return void
     *
     * @Route("/widgets", methods={"GET"}, name="admin-packages-widgets")
     */
    public function widgetsAction()
    {
        $this->view->packages = $this->_getPackages(Manager::PACKAGE_TYPE_WIDGET);
    }

    /**
     * Plugins action.
     *
     * @Route("/plugins", methods={"GET"}, name="admin-packages-plugins")
     */
    public function pluginsAction()
    {
        $this->view->packages = $this->_getPackages(Manager::PACKAGE_TYPE_PLUGIN);
    }

    /**
     * Libraries action.
     *
     * @return void
     *
     * @Route("/libraries", methods={"GET"}, name="admin-packages-libraries")
     */
    public function librariesAction()
    {
        $this->view->packages = $this->_getPackages(Manager::PACKAGE_TYPE_LIBRARY);
    }

    /**
     * Upload package action.
     *
     * @return void
     *
     * @Route("/upload", methods={"GET", "POST"}, name="admin-packages-upload")
     */
    public function uploadAction()
    {
        $this->view->form = $form = new UploadForm();

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $packageManager = new Manager(Package::find());
        $packageManager->clearTempDirectory();

        $packageFile = $this->request->getUploadedFiles();
        if (count($packageFile) == 1 && $packageFile[0]->getSize() != 0) {
            $filename = $packageManager->getTempDirectory() . 'uploaded.zip';
            $packageFile[0]->moveTo($filename);
            try {
                // Install package - check dep, copy files, get manifest, etc.
                $manifest = $packageManager->installPackage($filename);

                // Create package database object.
                if (!$manifest->isUpdate) {
                    $package = new Package();
                    $package->assign($manifest->toArray());
                    $package->save();

                    $this->_enablePackageConfig($package);
                    $this->_updateMetadata();

                    // install package dependencies
                    if ($manifest->get('dependencies')) {
                        $dependencies = $manifest->get('dependencies');
                        foreach ($dependencies as $dependency) {
                            $needPackage = $this->_getPackage($dependency['type'], $dependency['name']);
                            if ($needPackage) {
                                $packageDependency = new PackageDependency();
                                $packageDependency->package_id = $package->id;
                                $packageDependency->dependency_id = $needPackage->id;
                                $packageDependency->save();
                            }
                        }
                    }
                }

                if ($manifest->type == Manager::PACKAGE_TYPE_MODULE) {
                    // Run module install script.
                    $newPackageVersion = $packageManager->runInstallScript($manifest);
                    $this->_clearCache();

                    // Install translations if possible.
                    if (!empty($manifest->i18n)) {
                        foreach ($manifest->i18n as $languageData) {
                            Language::parseImportData($this->getDI(), $languageData->toArray());
                        }
                    }

                    // Register module in system to perform database update.
                    $modules = $this->getDI()->get('registry')->modules;
                    $loader = $this->getDI()->get('loader');
                    $modules[] = $manifest->name;
                    $moduleName = ucfirst($manifest->name);

                    // Register namespaces.
                    $loader->registerNamespaces(
                        [$moduleName => $this->getDI()->get('registry')->directories->modules . $moduleName],
                        true
                    );
                    $loader->register();

                    // Register module in app
                    $this->getDI()->get('app')->registerModules([$manifest->name => $moduleName . '\Bootstrap']);
                    $this->getDI()->get('registry')->modules = $modules;

                    // Update database.
                    $schema = new Schema($this->getDI());
                    $schema->updateDatabase();
                }

                if ($manifest->isUpdate) {
                    $this->flash->success('Package updated to version ' . $newPackageVersion . '!');
                } else {
                    $this->flash->success('Package installed!');
                }

            } catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
        } else {
            $this->flash->notice('Please, select zip file...');
        }
    }

    /**
     * Create package action.
     *
     * @return mixed
     *
     * @Route("/create", methods={"GET", "POST"}, name="admin-packages-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new CreateForm();
        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $data = $form->getValues();
        /** @var Package $package */
        $package = $form->getEntity();
        $package->save();
        $this->_setWidgetData($form, $package, $data);

        if (!empty($data['header'])) {
            $data['header'] = PHP_EOL . trim($data['header']) . PHP_EOL;
        }

        $packageManager = new Manager();
        $packageManager->createPackage($data);
        $this->_enablePackageConfig($package);
        $this->_updateMetadata();

        switch ($package->type) {
            case Manager::PACKAGE_TYPE_MODULE:
                $return = 'admin-packages';
                break;
            case Manager::PACKAGE_TYPE_THEME:
                $return = 'admin-packages-themes';
                break;
            case Manager::PACKAGE_TYPE_WIDGET:
                $return = 'admin-packages-widgets';
                break;
            case Manager::PACKAGE_TYPE_PLUGIN:
                $return = 'admin-packages-plugins';
                break;
            case Manager::PACKAGE_TYPE_LIBRARY:
                $return = 'admin-packages-libraries';
                break;
            default:
                $return = 'admin-packages';
                break;
        }

        $this->flashSession->success('New package created successfully!');
        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Edit package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/edit/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET", "POST"},
     * name="admin-packages-edit"
     * )
     */
    public function editAction($type, $name, $return)
    {
        $package = $this->_getPackage($type, $name);
        if (!$package) {
            return $this->response->redirect(['for' => $return]);
        }

        $this->view->form = $form = new EditForm($package, $return);

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $data = $form->getValues();
        $package = $form->getEntity();
        $this->_setWidgetData($form, $package, $data);
        $package->save();
        $this->_updateMetadata();

        $this->flashSession->success('Package saved!');
        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Events package.
     * Only for modules and plugins.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/events/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET", "POST"},
     * name="admin-packages-events"
     * )
     */
    public function eventsAction($type, $name, $return)
    {
        $package = $this->_getPackage($type, $name);
        if (!$package) {
            return $this->response->redirect(['for' => $return]);
        }

        if (!$package->enabled) {
            $this->flashSession->notice('Package must be enabled!');
            return $this->response->redirect(['for' => $return]);
        }

        $data = $package->getData();
        $postData = $this->request->getPost();
        if (!empty($postData)) {
            $data = ['events' => $postData];
        } elseif (!empty($data) && !empty($data['events'])) {
            $preparedData = [];
            $i = 0;
            foreach ($data['events'] as $event) {
                list ($key, $value) = explode('=', $event);
                $preparedData['event'][$i] = $value;
                $preparedData['class'][$i] = $key;
                $i++;
            }
            $data = ['events' => $preparedData];
        }

        $this->view->form = $form = new EventsForm($data, $package, $return);

        if (!$this->request->isPost() || !$form->isEventsDataValid()) {
            return;
        }

        if (!is_array($package->data)) {
            $package->data = [];
        }
        $package->data['events'] = $form->getEventsData();
        $package->save();
        $this->_updateMetadata();

        $this->flashSession->success('Package events saved!');
        return $this->response->redirect(['for' => $return]);
    }


    /**
     * Export package.
     *
     * @param string $type Package type.
     * @param string $name Package name.
     *
     * @return mixed
     *
     * @Route(
     * "/export/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}",
     * methods={"GET", "POST"},
     * name="admin-packages-export"
     * )
     */
    public function exportAction($type, $name)
    {
        $package = $this->_getPackage($type, $name);
        if (!$package) {
            return $this->response->redirect(['for' => 'admin-packages']);
        }

        $this->disableFooter();
        $this->view->form = $form = new ExportForm($package, ['name' => $name, 'type' => $type]);

        $skipForm = ($type == Manager::PACKAGE_TYPE_THEME);
        if (!$skipForm && (!$this->request->isPost() || !$form->isValid())) {
            return;
        }

        $this->view->disable();
        if ($package) {
            $dependencies = $form->getValues();
            $dependenciesData = [];

            /**
             * Collect modules.
             */
            if (!empty($dependencies['modules'])) {
                foreach ($dependencies['modules'] as $dependency) {
                    $depPackage = $this->_getPackage(Manager::PACKAGE_TYPE_MODULE, $dependency);

                    $dependenciesData[] = [
                        'name' => $dependency,
                        'type' => Manager::PACKAGE_TYPE_MODULE,
                        'version' => $depPackage->version,
                    ];
                }
            }

            /**
             * Collect libraries.
             */
            if (!empty($dependencies['libraries'])) {
                foreach ($dependencies['libraries'] as $dependency) {
                    $depPackage = $this->_getPackage(Manager::PACKAGE_TYPE_LIBRARY, $dependency);

                    $dependenciesData[] = [
                        'name' => $dependency,
                        'type' => Manager::PACKAGE_TYPE_LIBRARY,
                        'version' => $depPackage->version,
                    ];
                }
            }

            $package->setDependencies($dependenciesData);

            $packageManager = new Manager();
            $packageManager->exportPackage($package, ['withTranslations' => $form->getValue('withTranslations')]);
        }
    }

    /**
     * Uninstall package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/uninstall/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET"},
     * name="admin-packages-uninstall"
     * )
     */
    public function uninstallAction($type, $name, $return)
    {
        $this->view->disable();
        $package = $this->_getPackage($type, $name);
        if ($package) {
            if ($this->_hasDependencies($package)) {
                return $this->response->redirect(['for' => $return]);
            }

            try {
                if ($package->type == Manager::PACKAGE_TYPE_MODULE) {
                    $installerClass = ucfirst($name) . '\Installer';
                    if (class_exists($installerClass)) {
                        $packageInstaller = new $installerClass($this->di, $name);
                        if (method_exists($packageInstaller, 'remove')) {
                            $packageInstaller->remove();
                        }
                    }
                }

                $packageManager = new Manager();
                $packageManager->removePackage($package);
                $package->delete();

                $this->_removePackageConfig($package);
                $this->_updateMetadata();
                $this->_clearCache();

                // Update database.
                $schema = new Schema($this->getDI());
                $schema->updateDatabase(true);

                $this->flashSession->success('Package "' . $name . '" removed!');
            } catch (PackageException $e) {
                $package->delete();
                $this->flashSession->notice('Failed to remove package directory, check logs...');
            }
        } else {
            $this->flashSession->notice('Package not found...');
        }

        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Enable package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/enable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET"},
     * name="admin-packages-enable"
     * )
     */
    public function enableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            $package->enabled = 1;
            $package->save();

            $this->_enablePackageConfig($package);
            $this->_updateMetadata();
            $this->_clearCache();
        }

        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Disable package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/disable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET"},
     * name="admin-packages-disable"
     * )
     */
    public function disableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            if ($this->_hasDependencies($package)) {
                return $this->response->redirect(['for' => $return]);
            }

            $package->enabled = 0;
            $package->save();

            $this->_disablePackageConfig($package);
            $this->_updateMetadata();
            $this->_clearCache();
        }

        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Get package.
     *
     * @param string $type Package type.
     * @param string $name Package name.
     *
     * @return Package
     */
    protected function _getPackage($type, $name)
    {
        $query = $this->modelsManager->createBuilder()
            ->from(['t' => '\Core\Model\Package'])
            ->where("t.type = :type: AND t.name = :name:", ['type' => $type, 'name' => $name]);

        return $query->getQuery()->execute()->getFirst();
    }

    /**
     * Get packages by type.
     *
     * @param string $type Packages type.
     *
     * @return Package[]
     */
    protected function _getPackages($type)
    {
        return Package::findByType($type, null, 'enabled DESC');
    }

    /**
     * Remove package from config.
     *
     * @param Package $package Package object.
     *
     * @return void
     */
    protected function _removePackageConfig($package)
    {
        switch ($package->type) {
            case Manager::PACKAGE_TYPE_MODULE:
                // remove widgets
                $this->db->delete(Widget::getTableName(), 'module = ?', [$package->name]);
                break;
            case Manager::PACKAGE_TYPE_WIDGET:
                if ($widget = $package->getWidget()) {
                    $widget->delete();
                }
                break;
            case Manager::PACKAGE_TYPE_THEME:
            case Manager::PACKAGE_TYPE_PLUGIN:
                break;
        }
    }

    /**
     * Enable package in config.
     *
     * @param Package $package Package object.
     *
     * @return void
     */
    protected function _enablePackageConfig(Package $package)
    {
        switch ($package->type) {
            case Manager::PACKAGE_TYPE_MODULE:
                $data = $package->getData();
                // Install widgets.
                if (!empty($data['widgets'])) {
                    $errors = [];
                    foreach ($data['widgets'] as $widgetData) {
                        $widget = new Widget();
                        try {
                            $widget->save($widgetData);
                        } catch (\PDOException $e) {
                            $this->flash->notice('Failed to install module widget... Check logs.');
                            PackageException::exception($e);
                        }
                        if ($widget->validationHasFailed()) {
                            $messages = $widget->getMessages();
                            foreach ($messages as $message) {
                                $errors[] = $message->getMessage();
                            }
                        }
                    }

                    if (!empty($errors)) {
                        $this->flash->notice(
                            'There was some errors during installation:' . implode('<br/> - ', $errors)
                        );
                    }
                }

                // Enable module widgets.
                $this->db->update(Widget::getTableName(), ['enabled'], [1], "module = '{$package->name}'");
                break;
            case Manager::PACKAGE_TYPE_WIDGET:
                if ($widget = $package->getWidget()) {
                    $widget->enabled = 1;
                    $widget->save();
                } else {
                    $widget = new Widget();
                    $widget->assign($package->toArray());

                    // Check widget data.
                    $data = $package->getData();
                    if (!empty($data['module'])) {
                        $widget->module = $data['module'];
                    }

                    $widget->save();
                    $package->addData('widget_id', $widget->id);
                    $package->save();
                }
                break;
        }
    }

    /**
     * Disable package in config.
     *
     * @param Package $package Package object.
     *
     * @return void
     */
    protected function _disablePackageConfig(Package $package)
    {
        switch ($package->type) {
            case Manager::PACKAGE_TYPE_MODULE:
                // Disable module widgets.
                $this->db->update(Widget::getTableName(), ['enabled'], [0], "module = '{$package->name}'");
                break;
            case Manager::PACKAGE_TYPE_WIDGET:
                if ($widget = $package->getWidget()) {
                    $widget->enabled = 0;
                    $widget->save();
                }
                break;
        }
    }

    /**
     * Check if current package has dependencies.
     *
     * @param Package $package Package object.
     *
     * @return bool
     */
    protected function _hasDependencies(Package $package)
    {
        $dependencies = $package->getRelatedPackages();
        /** @var \Phalcon\Mvc\Model\Resultset\Simple $dependencies */
        if ($dependencies->count()) {
            $message = 'You can\'t uninstall or disable this package, because it\'s related to:<br/>';
            foreach ($dependencies as $dependency) {
                $dependencyPackage = $dependency->getPackage();
                $message .= " - {$dependencyPackage->type} '{$dependencyPackage->name}'";
            }
            $this->flashSession->error($message);

            return true;
        }

        return false;
    }

    /**
     * Set widget data.
     *
     * @param CoreForm $form    Form object.
     * @param Package  $package Package object.
     * @param array    $data    Post data.
     *
     * @return void
     */
    protected function _setWidgetData(CoreForm $form, Package $package, $data)
    {
        if (!$form->hasEntity('widget')) {
            return;
        }
        $widget = $form->getEntity('widget');
        $widget->name = ucfirst($widget->name);
        $widget->admin_form = ($widget->admin_form == 'form_class' ? $data['form_class'] : $widget->admin_form);
        $widget->description =
            (!empty($data['description']) ? $data['description'] : ucfirst($widget->name) . ' widget.');
        $widget->save();

        /**
         * Setup dependency.
         */
        if ($widget->module) {
            $package->data = [
                'module' => $widget->module,
                'widget_id' => $widget->id
            ];

            $module = $this->_getPackage(Manager::PACKAGE_TYPE_MODULE, $widget->module);
            $module->addData(
                'widgets', [
                    'name' => $widget->name,
                    'module' => $module->name,
                    'description' => $widget->description,
                    'is_paginated' => $widget->is_paginated,
                    'is_acl_controlled' => $widget->is_acl_controlled,
                    'admin_form' => $widget->admin_form,
                    'enabled' => (bool)$widget->enabled
                ],
                true
            );
            $module->save();

            $dependency = new PackageDependency();
            $dependency->package_id = $package->id;
            $dependency->dependency_id = $module->id;
            $dependency->save();
        } else {
            $package->addData('widget_id', $widget->id);
        }

        $package->save();
    }

    /**
     * Update packages metadata.
     *
     * @return void
     */
    protected function _updateMetadata()
    {
        $packageManager = new Manager();
        $packageManager->generateMetadata(Package::find());
    }
}