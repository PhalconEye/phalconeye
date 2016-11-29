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

use Core\Form\Backoffice\Setting\SettingSystemForm;
use Core\Model\SettingsModel;

/**
 * Admin settings.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/settings")
 */
class SettingsController extends AbstractBackofficeController
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET", "POST"}, name="backoffice-settings")
     */
    public function indexAction()
    {
        $form = new SettingSystemForm();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        foreach ($form->getValues() as $key => $value) {
            if ($key == 'theme') {
                $config = $this->getDI()->getConfig();
                $config->core->assets->offsetSet('theme', $value);
                $config->save();
                continue;
            }

            SettingsModel::setValue('system', $key, $value);
        }

        $this->flash->success('Settings saved!');
    }

}

