<?php

class Form_Admin_Languages_Edit extends Form_Admin_Languages_Create
{

    public function init()
    {
        $this
            ->setOption('title', "Edit Language")
            ->setOption('description', "Edit this language.");


        $this->addButton('Save', true);
        $this->addButtonLink('Cancel', '/admin/languages');

    }
}