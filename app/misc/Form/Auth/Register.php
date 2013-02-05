<?php

class Form_Auth_Register extends Form
{
    public function init()
    {
        $this
            ->setOption('title', "Register")
            ->setOption('description', "Register your account!");


        $this->addElement('textField', 'username', array(
            'label' => 'Username',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(2)
            )
        ));

        $this->addElement('textField', 'email', array(
            'label' => 'Email',
            'required' => true,
            'validators' => array(
                new Validator_Email()
            )
        ));

        $this->addElement('passwordField', 'password', array(
            'label' => 'Password',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(6)
            )
        ));

        $this->addElement('passwordField', 'repeatPassword', array(
            'label' => 'Password Repeat',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(6)
            )
        ));

        $this->addButton('Register', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/'; return false;"
        ));

    }
}