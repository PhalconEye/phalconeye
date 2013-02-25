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