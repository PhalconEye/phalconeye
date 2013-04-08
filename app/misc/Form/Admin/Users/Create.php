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

    }

    public function init()
    {
        $this
            ->setOption('title', "User Creation")
            ->setOption('description', "Create new user.");


        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'autocomplete' => 'off'
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'autocomplete' => 'off'
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email'
        ));

        $this->addElement('select', 'role_id', array(
            'label' => 'Role',
            'description' => 'Select user role',
            'options' => Role::find(),
            'using' => array('id', 'name')
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users'));

    }
}