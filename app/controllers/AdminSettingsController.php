<?php

class AdminSettingsController extends Controller
{

    public function indexAction()
    {
        $form = new Form_Admin_Settings_System();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)){
            return;
        }

        $data = $form->getData();
        Settings::setSettings($data);
    }
}

