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

class Widget_Header_Controller extends Widget_Controller{

    public function indexAction(){
        $this->view->setVar('site_title', Settings::getSetting('system_title', ''));
        $this->view->setVar('logo', $this->getParam('logo'));
        $this->view->setVar('show_title', $this->getParam('show_title'));
        $this->view->setVar('show_auth', $this->getParam('show_auth'));
    }

}