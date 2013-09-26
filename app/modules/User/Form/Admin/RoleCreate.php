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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace User\Form\Admin;

class RoleCreate extends \Engine\Form
{
    public function __construct($model = null)
    {
        if ($model === null){
            $model = new \User\Model\Role();
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
            'label' => 'Description'
        ));

        $this->addElement('check', 'is_default', array(
            'label' => 'Is Default',
            'options' => 1
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users-roles'));

    }
}