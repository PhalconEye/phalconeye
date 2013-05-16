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

namespace User\Form\Auth;

class Login extends \Engine\Form
{
    public function init()
    {
        $this
            ->setOption('title', "Login")
            ->setOption('description', "Use you email or username to login.")
            ->setAttrib('class', 'form_login')
            ->setAttrib('autocomplete', 'off')
        ;


        $this->addElement('text', 'login', array(
            'label' => 'Login (email or username)',
            'required' => true
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true
        ));

        $this->addButton('Login', true);
        $this->addButtonLink('Register', array('for' => 'register'));

    }
}