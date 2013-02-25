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

class Form_Auth_Login extends Form
{
    public function init()
    {
        $this
            ->setOption('title', "Login")
            ->setOption('description', "Use you email or username to login.")
            ->setAttrib('class', 'form_login');
        ;


        $this->addElement('textField', 'login', array(
            'label' => 'Login (email or username)',
            'required' => true
        ));

        $this->addElement('passwordField', 'password', array(
            'label' => 'Password',
            'required' => true
        ));

        $this->addButton('Login', true);
        $this->addButton('Register', false, array(
            'onclick' => "window.location.href='/register'; return false;"
        ));

    }
}