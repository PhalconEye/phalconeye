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

namespace User\Form\Backoffice\User;

use Core\Form\CoreForm;
use Engine\Db\AbstractModel;
use Engine\Form\FieldSet;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use User\Model\RoleModel;
use User\Model\UserModel;

/**
 * Create user.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UserCreateForm extends CoreForm
{
    /**
     * Create form.
     *
     * @param AbstractModel $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new UserModel();
        }

        $this->addEntity($entity);
    }

    /**
     * Add elements to form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('User Creation')
            ->setDescription('Create new user.')
            ->setAttribute('autocomplete', 'off');

        $content = $this->addContentFieldSet()
            ->addText('username', null, null, null, [], ['autocomplete' => 'off'])
            ->addPassword('password', null, null, [], ['autocomplete' => 'off'])
            ->addText('email', null, null, null, [], ['autocomplete' => 'off'])
            ->addSelect('role_id', 'Role', 'Select user role', RoleModel::find(), null, ['using' => ['id', 'name']]);

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-users']);

        $this->_setValidation($content);
    }

    /**
     * Set form validation.
     *
     * @param FieldSet $content Content fieldset.
     *
     * @return void
     */
    protected function _setValidation($content)
    {
        $content->getValidation()
            ->add('email', new Email())
            ->add('username', new PresenceOf())
            ->add('password', new PresenceOf());
    }
}