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

/**
 * @RoutePrefix("/admin/settings", name="admin-settings")
 */
class AdminSettingsController extends \Core\Controller\BaseAdmin
{
    /**
     * @Route("/", methods={"GET", "POST"}, name="admin-settings-general")
     */
    public function indexAction()
    {
        $form = new \Core\Form\Admin\Setting\System();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {

            return;
        }

        $values = $form->getValues();
        \Core\Model\Settings::setSettings($values);

        $this->flash->success('Settings saved!');
    }

}

