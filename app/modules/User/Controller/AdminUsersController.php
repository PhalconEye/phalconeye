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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace User\Controller;

/**
 * @RoutePrefix("/admin/users", name="admin-users")
 */
class AdminUsersController extends \Core\Controller\BaseAdmin
{
    public function init()
    {
        $navigation = new \Engine\Navigation();
        $navigation
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/users',
                    'title' => 'Users',
                    'prepend' => '<i class="icon-user icon-white"></i>'
                ),
                'roles' => array(
                    'href' => 'admin/users/roles',
                    'title' => 'Roles',
                    'prepend' => '<i class="icon-share icon-white"></i>'
                ),
                2 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'create' => array(
                    'href' => 'admin/users/create',
                    'title' => 'Create new user',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                ),
                'rolesCreate' => array(
                    'href' => 'admin/users/roles-create',
                    'title' => 'Create new role',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                )
            ));

        $this->view->navigation = $navigation;

    }

    /**
     * @Get("/", name="admin-users")
     */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $builder = $this->modelsManager->createBuilder()
            ->from('\User\Model\User');

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            array(
                "builder" => $builder,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->paginator = $page;
    }

    /**
     * @Route("/create", methods={"GET", "POST"}, name="admin-users-create")
     */
    public function createAction()
    {
        $form = new \Core\Form\Admin\User\Create();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $user = $form->getValues();
        $user->setPassword($this->security->hash($user->getPassword()));
        $user->role_id = \User\Model\Role::getDefaultRole()->getId();
        $user->save();

        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(array('for' => 'admin-users'));
    }

    /**
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-users-edit")
     */
    public function editAction($id)
    {
        $item = \User\Model\User::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => 'admin-users'));


        $form = new \Core\Form\Admin\User\Edit($item);
        $this->view->form = $form;

        $lastPassword = $item->getPassword();

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $user = $form->getValues();
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
        $item = \User\Model\User::findFirst($id);
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
     * @Get("/roles", name="admin-users-roles")
     */
    public function rolesAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $builder = $this->modelsManager->createBuilder()
            ->from('\User\Model\Role');

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            array(
                "builder" => $builder,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->paginator = $page;
    }

    /**
     * @Route("/roles-create", methods={"GET", "POST"}, name="admin-roles-create")
     */
    public function rolesCreateAction()
    {
        $form = new \Core\Form\Admin\User\RoleCreate();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        return;

        $item = $form->getValues();
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
        $item = \User\Model\Role::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => 'admin-users-roles'));


        $form = new \Core\Form\Admin\User\RoleEdit($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $item = $form->getValues();
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
        $item = \User\Model\Role::findFirst($id);
        if ($item) {
            if ($item->getIsDefault()) {
                $anotherRole = \User\Model\Role::findFirst();
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

