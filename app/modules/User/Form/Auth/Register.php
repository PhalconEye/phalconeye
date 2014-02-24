<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

use Core\Form\CoreForm;
use Engine\Form\FieldSet;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;

/**
 * Register form.
 *
 * @category  PhalconEye
 * @package   User\Form\Auth
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Register extends CoreForm
{
    /**
     * Add elements to form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Register')
            ->setDescription('Register your account!')
            ->setAttribute('autocomplete', 'off');

        $content = $this->addContentFieldSet()
            ->addText('username')
            ->addText(
                'email',
                null,
                'You will use your email address to login.',
                null,
                [],
                ['autocomplete' => 'off']
            )
            ->addPassword(
                'password',
                null,
                'Passwords must be at least 6 characters in length.',
                [],
                ['autocomplete' => 'off']
            )
            ->addPassword(
                'repeatPassword',
                null,
                'Enter your password again for confirmation.',
                [],
                ['autocomplete' => 'off']
            );

        $this->addFooterFieldSet()
            ->addButton('register')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'home']);

        $this->_setValidation($content);
    }

    /**
     * Set form validation.
     *
     * @param FieldSet $content Fieldset object.
     *
     * @return void
     */
    protected function _setValidation($content)
    {
        $content->getValidation()
            ->add('username', new StringLength(['min' => 2]))
            ->add('email', new Email())
            ->add('password', new StringLength(['min' => 6]))
            ->add('repeatPassword', new StringLength(['min' => 6]));

        $content
            ->setRequired('username')
            ->setRequired('email')
            ->setRequired('password')
            ->setRequired('repeatPassword');

        $this
            ->addFilter('password', self::FILTER_STRING)
            ->addFilter('repeatPassword', self::FILTER_STRING);
    }
}