<?php

class Widget_Header_Controller extends Widget_Controller{

    public function indexAction(){
        $this->view->setVar('site_title', Settings::getSetting('system_title', ''));
        $this->view->setVar('logo', $this->getParam('logo'));
        $this->view->setVar('show_title', $this->getParam('show_title'));
        $this->view->setVar('show_auth', $this->getParam('show_auth'));
    }

}