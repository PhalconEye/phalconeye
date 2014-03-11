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

namespace User\Form\Admin;

use Core\Form\CoreForm;
use Engine\Db\AbstractModel;
use Engine\Form\FieldSet;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use User\Model\Role;
use User\Model\User;

/**
 * Create user.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Create extends CoreForm
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
            $entity = new User();
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
            ->addSelect('role_id', 'Role', 'Select user role', Role::find(), null, ['using' => ['id', 'name']]);

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-users']);

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