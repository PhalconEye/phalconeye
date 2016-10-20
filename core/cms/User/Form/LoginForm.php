<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace User\Form\Auth;

use Core\Form\CoreForm;

/**
 * Login form.
 *
 * @category  PhalconEye
 * @package   User\Form\Auth
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class LoginForm extends CoreForm
{
    /**
     * Add elements to form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Login')
            ->setDescription('Use you email or username to login.')
            ->setAttribute('class', 'form_login');

        $this->addContentFieldSet()
            ->addText('login')
            ->addPassword('password')
            ->setRequired('login')
            ->setRequired('password');

        $this->addFooterFieldSet()
            ->addButton('enter')
            ->addButtonLink('register', 'Register', ['for' => 'register']);
    }
}