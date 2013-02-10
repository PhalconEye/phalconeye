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

    public function performanceAction(){
        $form = new Form_Admin_Settings_Performance();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)){
            return;
        }

        $data = $form->getData();
        if (!empty($data['clear_cache']) && $data['clear_cache'] = 1){
            $keys = $this->cacheOutput->queryKeys();
            foreach ($keys as $key) {
                $this->cacheOutput->delete($key);
            }

            $keys = $this->cacheData->queryKeys();
            foreach ($keys as $key) {
                $this->cacheData->delete($key);
            }

            $keys = $this->modelsCache->queryKeys();
            foreach ($keys as $key) {
                $this->modelsCache->delete($key);
            }

            $form->addNotice('Cache cleared!');
            $form->setElementParam('clear_cache', 'value', null);
        }
    }
}

