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

namespace User\Form\Admin;

use Engine\Db\AbstractModel;
use Engine\Form;
use User\Model\Role;
use User\Model\User;

/**
 * Create user.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Create extends Form
{
    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     */
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new User();
        }

        parent::__construct($model);
    }

    /**
     * Add elements to form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('title', "User Creation")
            ->setOption('description', "Create new user.")
            ->setAttrib('autocomplete', 'off');


        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'autocomplete' => 'off'
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'autocomplete' => 'off'
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email'
        ));

        $this->addElement('select', 'role_id', array(
            'label' => 'Role',
            'description' => 'Select user role',
            'options' => Role::find(),
            'using' => array('id', 'name')
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-users'));
    }
}