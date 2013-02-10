<?php

class Form_Admin_Menus_Create extends Form
{

    public function __construct($model = null)
    {

        if ($model === null){
            $model = new Menu();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Menu Creation")
            ->setOption('description', "Create new menu.");


        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', '/admin/menus');

    }
}