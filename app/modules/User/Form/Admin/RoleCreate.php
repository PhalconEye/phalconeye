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

/**
 * Create role.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RoleCreate extends Form
{
    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     */
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new Role();
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
            ->setOption('title', "Role Creation")
            ->setOption('description', "Create new role.");

        $this->addElement(
            'text',
            'name',
            [
                'label' => 'name',
            ]
        );

        $this->addElement(
            'textArea',
            'description',
            [
                'label' => 'Description'
            ]
        );

        $this->addElement(
            'check',
            'is_default',
            [
                'label' => 'Is Default',
                'options' => 1
            ]
        );

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', ['for' => 'admin-users-roles']);
    }
}