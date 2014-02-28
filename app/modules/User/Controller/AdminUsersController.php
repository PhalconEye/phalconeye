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

namespace User\Controller;

use Core\Controller\AbstractAdminController;
use Core\Form\EntityForm;
use Core\Form\TextForm;
use Engine\Navigation;
use User\Controller\Grid\Admin\RoleGrid;
use User\Controller\Grid\Admin\UserGrid;
use User\Form\Admin\Create as CreateForm;
use User\Form\Admin\Edit as EditForm;
use User\Form\Admin\RoleCreate as RoleCreateForm;
use User\Form\Admin\RoleEdit as RoleEditForm;
use User\Model\Role;
use User\Model\User;

/**
 * Manage users.
 *
 * @category  PhalconEye
 * @package   User\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/users", name="admin-users")
 */
class AdminUsersController extends AbstractAdminController
{
    /**
     * Init navigation.
     *
     * @return void
     */
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItems(
                [
                    'index' => [
                        'href' => 'admin/users',
                        'title' => 'Users',
                        'prepend' => '<i class="glyphicon glyphicon-user"></i>'
                    ],
                    'roles' => [
                        'href' => 'admin/users/roles',
                        'title' => 'Roles',
                        'prepend' => '<i class="glyphicon glyphicon-share"></i>'
                    ],
                    2 => [
                        'href' => 'javascript:;',
                        'title' => '|'
                    ],
                    'create' => [
                        'href' => 'admin/users/create',
                        'title' => 'Create new user',
                        'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                    ],
                    'rolesCreate' => [
                        'href' => 'admin/users/roles-create',
                        'title' => 'Create new role',
                        'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                    ]
                ]
            );

        $this->view->navigation = $navigation;

    }

    /**
     * Main action.
     *
     * @return void
     *
     * @Get("/", name="admin-users")
     */
    public function indexAction()
    {
        $grid = new UserGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Create new user.
     *
     * @return mixed
     *
     * @Route("/create", methods={"GET", "POST"}, name="admin-users-create")
     */
    public function createAction()
    {
        $form = new CreateForm();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid(null, true)) {
            return;
        }

        $user = $form->getEntity();
        $user->setPassword($user->password);
        $user->role_id = Role::getDefaultRole()->id;
        $user->save();

        $this->flashSession->success('New object created successfully!');

        return $this->response->redirect(['for' => 'admin-users']);
    }

    /**
     * Edit user.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-users-edit")
     */
    public function editAction($id)
    {
        $item = User::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => 'admin-users']);
        }

        $lastPassword = $item->password;
        $item->password = 'emptypassword';

        if (isset($_POST['password']) && $_POST['password'] == 'emptypassword') {
            $_POST['password'] = $item->password = $lastPassword;
        }

        $form = new EditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => 'admin-users']);
    }

    /**
     * View user details.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Get("/view/{id:[0-9]+}", name="admin-users-view")
     */
    public function viewAction($id)
    {
        $user = User::findFirst($id);
        $this->view->form = $form = TextForm::factory($user, [], [['password']]);

        $form
            ->setTitle('User details')
            ->addFooterFieldSet()
            ->addButtonLink('back', 'Back', ['for' => 'admin-users']);
    }

    /**
     * Delete user.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Get("/delete/{id:[0-9]+}", name="admin-users-delete")
     */
    public function deleteAction($id)
    {
        $item = User::findFirst($id);
        if ($item) {
            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(['for' => 'admin-users']);
    }

    /**
     * User roles.
     *
     * @return void
     *
     * @Get("/roles", name="admin-users-roles")
     */
    public function rolesAction()
    {
        $grid = new RoleGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Role creation.
     *
     * @return mixed
     *
     * @Route("/roles-create", methods={"GET", "POST"}, name="admin-roles-create")
     */
    public function rolesCreateAction()
    {
        $form = new RoleCreateForm();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $item = $form->getEntity();
        if ($item->is_default) {
            $this->db->update(
                $item->getSource(),
                ['is_default'],
                [0],
                "id != {$item->id}"
            );
        }
        $this->flashSession->success('New object created successfully!');

        return $this->response->redirect(['for' => 'admin-users-roles']);
    }

    /**
     * Edit role.
     *
     * @param int $id Role identity.
     *
     * @return mixed
     *
     * @Route("/roles-edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-roles-edit")
     */
    public function rolesEditAction($id)
    {
        $item = Role::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => 'admin-users-roles']);
        }

        $form = new RoleEditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $item = $form->getEntity();
        if ($item->is_default) {
            $this->db->update(
                Role::getTableName(),
                ['is_default'],
                [0],
                "id != {$item->id}"
            );
        }

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => 'admin-users-roles']);
    }

    /**
     * Delete role.
     *
     * @param int $id Role identity.
     *
     * @return mixed
     *
     * @Get("/roles-delete/{id:[0-9]+}", name="admin-roles-delete")
     */
    public function rolesDeleteAction($id)
    {
        $item = Role::findFirst($id);
        if ($item) {
            if ($item->is_default) {
                $anotherRole = Role::findFirst();
                if ($anotherRole) {
                    $anotherRole->is_default = 1;
                    $anotherRole->save();
                }
            }
            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(['for' => 'admin-users-roles']);
    }
}