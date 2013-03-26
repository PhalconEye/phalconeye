<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Form_Admin_Users_Create extends Form
{

    public function __construct($model = null)
    {

        if ($model === null){
            $model = new User();
        }

        parent::__construct($model);

        $this->setElementParam('role_id', 'options', Role::find());
        $this->setElementParam('role_id', 'using', array('id', 'name'));
        $this->setElementParam('role_id', 'description', 'Select user role');
        $this->setElementAttrib('role_id', 'order', 4);
    }

    public function init()
    {
        $this
            ->setOption('title', "User Creation")
            ->setOption('description', "Create new user.");


        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users'));

    }
}