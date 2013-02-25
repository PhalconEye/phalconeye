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
        $this
            ->addIgnored('type')
            ->addIgnored('undeletable')
        ;

        if ($model === null){
            $model = new Role();
        }

        parent::__construct($model);

        $this->setElementParam('is_default', 'value', 1);
    }

    public function init()
    {
        $this
            ->setOption('title', "Role Creation")
            ->setOption('description', "Create new role.");


        $this->addButton('Create', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/admin/users/roles'; return false;"
        ));

    }
}