<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Install;

use Engine\Form;
use Engine\Form\Validator\StringLength;
use Engine\Form\Validator\Email;

/**
 * Installation finish form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Install
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Finish extends Form
{
    /**
     * Setup form.
     *
     * @return void
     */
    public function init()
    {
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'autocomplete' => 'off',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 2,
                ))
            )
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'autocomplete' => 'off',
            'description' => 'You will use your email address to login.',
            'required' => true,
            'validators' => array(
                new Email()
            )
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'autocomplete' => 'off',
            'description' => 'Passwords must be at least 6 characters in length.',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addElement('password', 'repeatPassword', array(
            'label' => 'Password Repeat',
            'autocomplete' => 'off',
            'description' => 'Enter your password again for confirmation.',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $this->addButton('Complete', true);
    }
}