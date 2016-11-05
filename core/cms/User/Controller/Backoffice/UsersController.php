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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace User\Controller\Backoffice;

use Core\Controller\Backoffice\AbstractBackofficeController;
use Core\Form\TextForm;
use User\Form\Backoffice\Role\RoleCreateForm;
use User\Form\Backoffice\Role\RoleEditForm;
use User\Form\Backoffice\User\UserCreateForm;
use User\Form\Backoffice\User\UserEditForm;
use User\Grid\Backoffice\RoleGrid;
use User\Grid\Backoffice\UserGrid;
use User\Model\RoleModel;
use User\Model\UserModel;
use User\Navigation\Backoffice\UsersNavigation;

/**
 * Manage users.
 *
 * @category  PhalconEye
 * @package   User\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/users", name="backoffice-users")
 */
class UsersController extends AbstractBackofficeController
{
    /**
     * Init navigation.
     *
     * @return void
     */
    public function init()
    {
        $this->view->navigation = new UsersNavigation();
    }

    /**
     * Main action.
     *
     * @return void
     *
     * @Get("/", name="backoffice-users")
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
     * @Route("/create", methods={"GET", "POST"}, name="backoffice-users-create")
     */
    public function createAction()
    {
        $form = new UserCreateForm();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid(null, true)) {
            return;
        }

        $user = $form->getEntity();
        $user->setPassword($user->password);
        $user->role_id = RoleModel::getDefaultRole()->id;
        $user->save();

        $this->flashSession->success('New object created successfully!');

        return $this->response->redirect(['for' => 'backoffice-users']);
    }

    /**
     * Edit user.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-users-edit")
     */
    public function editAction($id)
    {
        $item = UserModel::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => 'backoffice-users']);
        }

        $lastPassword = $item->password;
        $item->password = 'emptypassword';

        if (isset($_POST['password']) && $_POST['password'] == 'emptypassword') {
            $_POST['password'] = $item->password = $lastPassword;
        }

        $form = new UserEditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => 'backoffice-users']);
    }

    /**
     * View user details.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Get("/view/{id:[0-9]+}", name="backoffice-users-view")
     */
    public function viewAction($id)
    {
        $user = UserModel::findFirst($id);
        $this->view->form = $form = TextForm::factory($user, [], [['password']]);

        $form
            ->setTitle('User details')
            ->addFooterFieldSet()
            ->addButtonLink('back', 'Back', ['for' => 'backoffice-users']);
    }

    /**
     * Delete user.
     *
     * @param int $id User identity.
     *
     * @return mixed
     *
     * @Get("/delete/{id:[0-9]+}", name="backoffice-users-delete")
     */
    public function deleteAction($id)
    {
        $item = UserModel::findFirst($id);
        if ($item) {
            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(['for' => 'backoffice-users']);
    }

    /**
     * User roles.
     *
     * @return void
     *
     * @Get("/roles", name="backoffice-roles")
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
     * @Route("/roles-create", methods={"GET", "POST"}, name="backoffice-roles-create")
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

        return $this->response->redirect(['for' => 'backoffice-roles']);
    }

    /**
     * Edit role.
     *
     * @param int $id Role identity.
     *
     * @return mixed
     *
     * @Route("/roles-edit/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-roles-edit")
     */
    public function rolesEditAction($id)
    {
        $item = RoleModel::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => 'backoffice-roles']);
        }

        $form = new RoleEditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $item = $form->getEntity();
        if ($item->is_default) {
            $this->db->update(
                RoleModel::getTableName(),
                ['is_default'],
                [0],
                "id != {$item->id}"
            );
        }

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => 'backoffice-roles']);
    }

    /**
     * Delete role.
     *
     * @param int $id Role identity.
     *
     * @return mixed
     *
     * @Get("/roles-delete/{id:[0-9]+}", name="backoffice-roles-delete")
     */
    public function rolesDeleteAction($id)
    {
        $item = RoleModel::findFirst($id);
        if ($item) {
            if ($item->is_default) {
                $anotherRole = RoleModel::findFirst();
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

        return $this->response->redirect(['for' => 'backoffice-roles']);
    }
}