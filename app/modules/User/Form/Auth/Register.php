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

class Register extends \Engine\Form
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
                new \Engine\Form\Validator\StringLength(array(
                    'min' => 2,
                ))
            )
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'validators' => array(
                new \Engine\Form\Validator\Email()
            )
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'validators' => array(
                new \Engine\Form\Validator\StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addElement('password', 'repeatPassword', array(
            'label' => 'Password Repeat',
            'required' => true,
            'validators' => array(
                new \Engine\Form\Validator\StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addButton('Register', true);
        $this->addButtonLink('Cancel', array('for' => 'home'));

    }
}