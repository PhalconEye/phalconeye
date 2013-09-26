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

namespace Core\Widget\Header;

class Controller extends \Engine\Widget\Controller{

    public function indexAction(){
        $this->view->site_title = \Core\Model\Settings::getSetting('system_title', '');
        $this->view->logo = $this->getParam('logo');
        $this->view->show_title = $this->getParam('show_title');
        $this->view->show_auth = $this->getParam('show_auth');
    }

}