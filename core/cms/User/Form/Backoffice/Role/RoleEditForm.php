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

namespace User\Form\Backoffice\Role;

/**
 * Edit role.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RoleEditForm extends RoleCreateForm
{
    /**
     * Add elements to form.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this
            ->setTitle('Edit Role')
            ->setDescription('Edit this role.');

        $this->getFieldSet(self::FIELDSET_FOOTER)
            ->clearElements()
            ->addButton('save')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-users-roles']);
    }
}