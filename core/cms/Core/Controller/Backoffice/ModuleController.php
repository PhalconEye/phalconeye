<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller\Backoffice;

use Core\Form\CoreForm;
use Core\Model\SettingsModel;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherAdminException;

/**
 * Admin modules controller.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/module", name="backoffice-module")
 */
class ModuleController extends AbstractBackofficeController
{
    const
        /**
         * Module config form namespace
         */
        CONFIG_FORM_NS = '\Form\Backoffice\SettingsForm';

    /**
     * Index action.
     *
     * @param string $module Module name
     *
     * @return mixed
     *
     * @Route("/{name:[a-zA-Z0-9_-]+}", methods={"GET", "POST"}, name="backoffice-module-index")
     */
    public function indexAction($module)
    {
        $this->_checkModuleExists($module);

        $configForm = $module . self::CONFIG_FORM_NS;

        if (!class_exists($configForm)) {
            return;
        }

        /** @var $form CoreForm */
        $this->view->form = $form = new $configForm;

        if (!$form instanceof CoreForm) {
            throw new \InvalidArgumentException('Config form must be instance of CoreForm');
        }

        if (!$this->request->isPost()) {
            $form->setValues(SettingsModel::getValue($module));
            return;
        }

        if (!$form->isValid()) {
            return;
        }

        foreach ($form->getValues() as $key => $value) {
            SettingsModel::setValue($module, $key, $value);
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
        $modules = $this->getDI()->getModules();

        if (!$modules->has($module)) {
            // todo: create DispatcherAdminException handler
            throw new DispatcherAdminException;
        }
    }
}
