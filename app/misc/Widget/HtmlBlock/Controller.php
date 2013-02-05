<?php

class Widget_HtmlBlock_Controller extends Widget_Controller{

    public function indexAction(){
        $this->view->setVar('title', $this->getParam('title'));

        $html = $this->getParam('html');
        if (empty($html))
            return $this->setNoRender();

        $this->view->setVar('html', $html);
    }

    public function adminAction(){
        $form = new Form();

        $form->addElement('textField', 'title', array(
            'label' => $this->di->get('trans')->_('Title')
        ));

        $form->addElement('textArea', 'html', array(
            'label' => $this->di->get('trans')->_('HTML block')
        ));

        return $form;
    }
}