<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

/**
 * @RoutePrefix("/admin/users", name="admin-users")
 */
class AdminUsersController extends AdminController
{
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/users',
                    'title' => 'Users'
                ),
                'roles' => array(
                    'href' => 'admin/users/roles',
                    'title' => 'Roles'
                ),
                2 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'create' => array(
                    'href' => 'admin/users/create',
                    'title' => 'Create new user'
                ),
                'rolesCreate' => array(
                    'href' => 'admin/users/roles-create',
                    'title' => 'Create new role'
                )
            ));

        $this->view->setVar('navigation', $navigation);

    }

    /**
     * @Get("/", name="admin-users")
     */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $users = User::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $users,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

    /**
     * @Route("/create", methods={"GET", "POST"}, name="admin-users-create")
     */
    public function createAction()
    {
        $form = new Form_Admin_Users_Create();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $user = $form->getData();
        $user->setPassword($this->security->hash($user->getPassword()));
        $user->role_id = Role::getDefaultRole()->getId();
        $user->save();

        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(array('for' => 'admin-users'));
    }

    /**
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-users-edit")
     */
    public function editAction($id)
    {
        $item = User::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => 'admin-users'));


        $form = new Form_Admin_Users_Edit($item);
        $this->view->setVar('form', $form);

        $lastPassword = $item->getPassword();

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $user = $form->getData();
        if ($lastPassword != $item->getPassword()) {
            $user->setPassword($this->security->hash($user->getPassword()));
            $user->save();
        }

        $this->flashSession->success('Object saved!');
        return $this->response->redirect(array('for' => 'admin-users'));
    }

    /**
     * @Get("/delete/{id:[0-9]+}", name="admin-users-delete")
     */
    public function deleteAction($id)
    {
        $item = User::findFirst($id);
        if ($item){
            if ($item->delete()){
                $this->flashSession->notice('Object deleted!');
            }
            else{
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(array('for' => 'admin-users'));
    }

    /**
     * @Get("/roles", name="admin-roles")
     */
    public function rolesAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $users = Role::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $users,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

    /**
     * @Route("/roles-create", methods={"GET", "POST"}, name="admin-roles-create")
     */
    public function rolesCreateAction()
    {
        $form = new Form_Admin_Users_RoleCreate();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();
        if ($item->getIsDefault()) {
            $this->db->update(
                $item->getSource(),
                array('is_default'),
                array(0),
                "id != {$item->getId()}"
            );
        }

        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(array('for' => 'admin-users-roles'));
    }

    /**
     * @Route("/roles-edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-roles-edit")
     */
    public function rolesEditAction($id)
    {
        $item = Role::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => 'admin-users-roles'));


        $form = new Form_Admin_Users_RoleEdit($item);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();
        if ($item->getIsDefault()) {
            $this->db->update(
                $item->getSource(),
                array('is_default'),
                array(0),
                "id != {$item->getId()}"
            );
        }

        $this->flashSession->success('Object saved!');
        return $this->response->redirect(array('for' => 'admin-users-roles'));
    }

    /**
     * @Get("/roles-delete/{id:[0-9]+}", name="admin-roles-delete")
     */
    public function rolesDeleteAction($id)
    {
        $item = Role::findFirst($id);
        if ($item) {
            if ($item->getIsDefault()) {
                $anotherRole = Role::findFirst();
                if ($anotherRole){
                    $anotherRole->setIsDefault(1);
                    $anotherRole->save();
                }
            }
            if ($item->delete()){
                $this->flashSession->notice('Object deleted!');
            }
            else{
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(array('for' => 'admin-users-roles'));
    }
}

