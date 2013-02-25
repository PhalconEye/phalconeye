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
//        $form->setAttrib('style', 'width: 700px;');

        $form->addElement('textField', 'title', array(
            'label' => $this->di->get('trans')->_('Title')
        ));

        $form->addElement('textArea', 'html', array(
            'label' => $this->di->get('trans')->_('HTML block')
        ));

        $form->addElement('html', 'ckeditor',
            array(
                'ignore' => true,
                'html' => '<script type="text/javascript">$(document).ready(function () {CKEDITOR.replace("html");});</script>'
            ), 1000);

        return $form;
    }
}