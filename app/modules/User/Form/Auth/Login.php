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

namespace User\Form\Auth;

use Engine\Form;

/**
 * Login form.
 *
 * @category  PhalconEye
 * @package   User\Form\Auth
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Login extends Form
{
    /**
     * Add elements to form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('title', "Login")
            ->setOption('description', "Use you email or username to login.")
            ->setAttrib('class', 'form_login')
            ->setAttrib('autocomplete', 'off');

        $this->addElement(
            'text',
            'login',
            [
                'label' => 'Username/Email',
                'required' => true
            ]
        );

        $this->addElement(
            'password',
            'password',
            [
                'label' => 'Password',
                'required' => true
            ]
        );

        $this->addButton('Login', true);
        $this->addButtonLink('Register', ['for' => 'register']);
    }
}