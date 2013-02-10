<?php

class Form_Admin_Menus_Edit extends Form_Admin_Menus_Create
{

    public function init()
    {
        $this
            ->setOption('title', "Edit Menu")
            ->setOption('description', "Edit this menu.");



        $this->addButton('Save', true);
        $this->addButtonLink('Cancel', '/admin/menus');

    }
}