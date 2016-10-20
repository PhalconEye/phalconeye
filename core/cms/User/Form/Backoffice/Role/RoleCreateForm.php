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

use Core\Form\CoreForm;
use Engine\Db\AbstractModel;
use User\Model\RoleModel;

/**
 * Create role.
 *
 * @category  PhalconEye
 * @package   User\Form\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RoleCreateForm extends CoreForm
{
    /**
     * Create form.
     *
     * @param AbstractModel|null $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new RoleModel();
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
            ->setTitle('Role Creation')
            ->setDescription('Create new role.');

        $this->addContentFieldSet()
            ->addText('name')
            ->addTextArea('description')
            ->addCheckbox('is_default', 'Is Default', null, 1, false, 0);

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-users-roles']);
    }
}