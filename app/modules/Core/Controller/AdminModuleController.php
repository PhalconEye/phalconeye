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

use Core\Form\CoreForm;
use Core\Model\Settings;
use Engine\Exception;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherAdminException;

/**
 * Admin modules controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/module", name="admin-module")
 */
class AdminModuleController extends AbstractAdminController
{
    const
        /**
         * Module config form namespace
         */
        CONFIG_FORM_NS = '\Form\ConfigForm';

    /**
     * Index action.
     *
     * @param string $module Module name
     *
     * @return mixed
     *
     * @Route("/{name:[a-zA-Z0-9_-]+}", methods={"GET", "POST"}, name="admin-module-index")
     */
    public function indexAction($module)
    {
        $this->_checkModuleExists($module);

        $configForm = ucfirst($module) . self::CONFIG_FORM_NS;

        if (!class_exists($configForm)) {
            return;
        }

        /** @var $form CoreForm */
        $this->view->form = $form = new $configForm;

        if (!$form instanceof CoreForm) {
            throw new Exception('Config form must be instance of CoreForm');
        }

        $form->setTitle(ucfirst($module) .' settings');
        $form->addFooterFieldSet()->addButton('save');

        if (!$this->request->isPost()) {
            foreach ($form->getValues() as $key => $default) {
                $form->setValue($key, Settings::getSetting($module . '_'. $key, $default));
            }
            return;
        }

        if (!$form->isValid()) {
            return;
        }

        foreach ($form->getValues() as $key => $value) {
            Settings::setSetting($module .'_'. $key, $value);
        }

        $this->flash->success('Settings saved!');
    }

    /**
     * Ensure module existence
     *
     * @param string $module Module name
     *
     * @return void
     * @throws DispatcherAdminException
     */
    protected function _checkModuleExists($module)
    {
        $modules = $this->getDI()->get('registry')->modules;

        if (!in_array($module, $modules)) {
            // todo: create DispatcherAdminException handler
            throw new DispatcherAdminException;
        }
    }
}