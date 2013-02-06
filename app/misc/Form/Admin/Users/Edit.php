<?php

class Form_Admin_Users_Edit extends Form_Admin_Users_Create
{

    public function init()
    {
        $this
            ->setOption('title', "Edit User")
            ->setOption('description', "Edit this user.");




        $this->addButton('Save', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/admin/users'; return false;"
        ));

    }
}