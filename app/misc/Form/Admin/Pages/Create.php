<?php

class Form_Admin_Pages_Create extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('view_count')
            ->addIgnored('layout')
        ;

        if ($model === null){
            $model = new Page();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Page Creation")
            ->setOption('description', "Create new page.");


        $this->addButton('Create', true);
        $this->addButton('Cancel', false, array(
            'onclick' => 'history.go(-1); return false;'
        ));

    }
}