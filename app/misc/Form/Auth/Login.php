<?php

class Form_Auth_Login extends Form
{
    public function init()
    {
        $this
            ->setOption('title', "Login")
            ->setOption('description', "Use you email or username to login.");


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