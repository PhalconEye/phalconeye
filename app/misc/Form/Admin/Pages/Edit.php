<?php

class Form_Admin_Pages_Edit extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('view_count')
            ->addIgnored('layout')
        ;

        if ($model === null){
            $model = new Pages();
        }

        parent::__construct($model);
    }

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