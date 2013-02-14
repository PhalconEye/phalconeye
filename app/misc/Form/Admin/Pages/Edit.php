<?php

class Form_Admin_Pages_Edit extends Form_Admin_Pages_Create
{

    public function init()
    {
        $this
            ->setOption('title', "Edit Page")
            ->setOption('description', "Edit this page.");


        $this->addButton('Save', true);
        $this->addButton('Cancel', false, array(
            'onclick' => 'history.go(-1); return false;'
        ));

    }
}