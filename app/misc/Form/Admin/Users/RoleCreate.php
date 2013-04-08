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

class Form_Admin_Users_RoleCreate extends Form
{

    public function __construct($model = null)
    {
        if ($model === null){
            $model = new Role();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Role Creation")
            ->setOption('description', "Create new role.");

        $this->addElement('text', 'name', array(
            'label' => 'name',
        ));

        $this->addElement('textArea', 'description', array(
            'label' => 'name'
        ));

        $this->addElement('check', 'is_default', array(
            'label' => 'Is Default',
            'options' => 1
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users-roles'));

    }
}