<?php

class Form_Admin_Languages_Create extends Form
{

    public function __construct($model = null)
    {
        if ($model === null){
            $model = new Language();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Language Creation")
            ->setOption('description', "Create new language.")
            ;


        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', '/admin/languages');

    }
}