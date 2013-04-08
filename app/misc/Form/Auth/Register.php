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

class Form_Auth_Register extends Form
{
    public function init()
    {
        $this
            ->setOption('title', "Register")
            ->setOption('description', "Register your account!");


        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(array(
                    'min' => 2,
                ))
            )
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'validators' => array(
                new Validator_Email()
            )
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addElement('password', 'repeatPassword', array(
            'label' => 'Password Repeat',
            'required' => true,
            'validators' => array(
                new Validator_StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addButton('Register', true);
        $this->addButtonLink('Cancel', array('for' => 'home'));

    }
}