<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

use Phalcon\Tag;
use Phalcon\Web\Tools;
use Phalcon\Builder\BuilderException;

class ControllersController extends ControllerBase
{

    public function indexAction()
    {
        if (Tools::getConfig()->modules) {
            $this->view->setVar('hasModules', true);
            $modules = array();
            foreach (Tools::getConfig()->modules as $moduleName => $enabled) {
                if (!$enabled) continue;
                $modules[$moduleName] = $moduleName;
            }
            $this->view->setVar('modules', $modules);
        } else {
            $this->view->setVar('hasModules', false);
            $this->view->setVar('controllersDir', Tools::getConfig()->application->controllersDir);
        }
    }

    /**
     * Generate controller
     */
    public function createAction()
    {

        if ($this->request->isPost()) {
            $moduleName = $this->request->getPost('module', 'string');
            $controllerName = $this->request->getPost('name', 'string');
            $force = $this->request->getPost('force', 'int');

            try {

                if ($moduleName) {
                    $controllerBuilder = new \Phalcon\Builder\Controller(array(
                        'name' => $controllerName,
                        'directory' => ROOT_PATH . "/app/modules/{$moduleName}/controllers",
                        'controllersDir' => ROOT_PATH . "/app/modules/{$moduleName}/controllers/",
                        'namespace' => $moduleName."\Controllers",
                        'baseClass' => Tools::getWebToolsConfig()->controllerBaseClass,
                        'force' => $force
                    ));

                    $fileName = $controllerBuilder->build();

                    $this->flash->success('The controller "' . $fileName . '" was created successfully');

                    return $this->dispatcher->forward(array(
                        'controller' => 'controllers',
                        'action' => 'edit',
                        'params' => array($fileName)
                    ));
                } else {
                    $controllerBuilder = new \Phalcon\Builder\Controller(array(
                        'name' => $controllerName,
                        'directory' => null,
                        'namespace' => null,
                        'baseClass' => Tools::getWebToolsConfig()->controllerBaseClass,
                        'force' => $force
                    ));

                    $fileName = $controllerBuilder->build();

                    $this->flash->success('The controller "' . $fileName . '" was created successfully');

                    return $this->dispatcher->forward(array(
                        'controller' => 'controllers',
                        'action' => 'edit',
                        'params' => array($fileName)
                    ));
                }

            } catch (BuilderException $e) {
                $this->flash->error($e->getMessage());
            }

        }

        return $this->dispatcher->forward(array(
            'controller' => 'controllers',
            'action' => 'index'
        ));

    }

    public function listAction()
    {
        if (Tools::getConfig()->modules) {
            $this->view->setVar('hasModules', true);
            $this->view->setVar('modules', Tools::getConfig()->modules);
        } else {
            $this->view->setVar('hasModules', false);
            $this->view->setVar('controllersDir', Tools::getConfig()->application->controllersDir);
        }
    }

    public function editAction($fileName)
    {
        $fileName = str_replace('..', '', $fileName);

        if (Tools::getConfig()->modules) {
            $controllersDir = ROOT_PATH . "/app/modules/{$this->request->get('_module', 'string')}/controllers/";
        } else {
            $controllersDir = Tools::getConfig()->application->controllersDir;
        }

        if (!file_exists($controllersDir . '/' . $fileName)) {
            $this->flash->error('Controller could not be found');
            return $this->dispatcher->forward(array(
                'controller' => 'controllers',
                'action' => 'list'
            ));
        }

        Tag::setDefault('code', file_get_contents($controllersDir . '/' . $fileName));
        Tag::setDefault('name', $fileName);
        $this->view->setVar('name', $fileName);
        $this->view->setVar('moduleName', $this->request->get('_module', 'string'));

    }

    public function saveAction()
    {

        if ($this->request->isPost()) {

            $fileName = $this->request->getPost('name', 'string');

            $fileName = str_replace('..', '', $fileName);

            if (Tools::getConfig()->modules) {
                $controllersDir = ROOT_PATH . "/app/modules/{$this->request->get('_module', 'string')}/controllers/";
            } else {
                $controllersDir = Tools::getConfig()->application->controllersDir;
            }

            if (!file_exists($controllersDir . '/' . $fileName)) {
                $this->flash->error('Controller could not be found');
                return $this->dispatcher->forward(array(
                    'controller' => 'controllers',
                    'action' => 'list'
                ));
            }

            if (!is_writable($controllersDir . '/' . $fileName)) {
                $this->flash->error('Controller file does not has write access');
                return $this->dispatcher->forward(array(
                    'controller' => 'controllers',
                    'action' => 'list'
                ));
            }

            file_put_contents($controllersDir . '/' . $fileName, $this->request->getPost('code'));

            $this->flash->success('The controller "' . $fileName . '" was saved successfully');
        }

        return $this->dispatcher->forward(array(
            'controller' => 'controllers',
            'action' => 'list'
        ));

    }


}