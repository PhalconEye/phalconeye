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

namespace Core\Form\Admin\User;

class Edit extends Create
{

    public function init()
    {
        parent::init();
        $this
            ->setOption('title', "Edit User")
            ->setOption('description', "Edit this user.");


        $this->clearButtons();
        $this->addButton('Save', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users'));

    }
}