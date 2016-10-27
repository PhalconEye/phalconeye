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

namespace Core\Widget\Header;

use Core\Form\Backoffice\Widget\WidgetHeaderForm;
use Core\Model\SettingsModel;
use Engine\Widget\Controller as WidgetController;

/**
 * Header widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Header
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{
    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->site_title = SettingsModel::getValue('system', 'title', '');
        $this->view->logo = $this->getParam('logo');
        $this->view->show_title = $this->getParam('show_title');
        $this->view->show_auth = $this->getParam('show_auth');
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminForm()
    {
        return new WidgetHeaderForm();
    }

    /**
     * {@inheritdoc}
     */
    public function isAclControlled()
    {
        return true;
    }
}