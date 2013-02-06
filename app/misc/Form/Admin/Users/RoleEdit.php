<?php

class Form_Admin_Users_RoleEdit extends Form_Admin_Users_RoleCreate
{

    public function init()
    {
        $this
            ->setOption('title', "Edit Role")
            ->setOption('description', "Edit this role.");




        $this->addButton('Save', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/admin/users/roles'; return false;"
        ));

    }
}