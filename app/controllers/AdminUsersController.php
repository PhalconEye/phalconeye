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

class AdminUsersController extends Controller
{
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'index' => array(
                'href' => 'admin/users',
                'title' => 'Users'
            ),
            'roles' => array(
                'href' => 'admin/users/roles',
                'title' => 'Roles'
            ),
//            'access' => array(
//                'href' => 'admin/access',
//                'title' => 'User access'
//            )
        ))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigationMain', $navigation);

        $navigation = new Navigation();
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'create' => array(
                'href' => 'admin/users/create',
                'title' => 'Create new user'
            ),
            'rolesCreate' => array(
                'href' => 'admin/users/roles-create',
                'title' => 'Create new role'
            )))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigationCreation', $navigation);
    }

    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $users = User::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $users,
                "limit"=> 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

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

        $this->response->redirect("admin/users");
    }

    public function editAction($id)
    {
        $item = User::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/users");


        $form = new Form_Admin_Users_Edit($item);
        $this->view->setVar('form', $form);

        $lastPassword = $item->getPassword();

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $user = $form->getData();
        if ($lastPassword != $item->getPassword()){
            $user->setPassword($this->security->hash($user->getPassword()));
            $user->save();
        }

        $this->response->redirect("admin/users");
    }

    public function deleteAction($id)
    {
        $item = User::findFirst($id);
        if ($item)
            $item->delete();

        return $this->response->redirect("admin/users");
    }

    public function rolesAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $users = Role::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $users,
                "limit"=> 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

    public function rolesCreateAction()
    {
        $form = new Form_Admin_Users_RoleCreate();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();
        if ($item->getIsDefault()){
            $this->db->update(
                $item->getSource(),
                array('is_default'),
                array(0),
                "id != {$item->getId()}"
            );
        }

        $this->response->redirect("admin/users/roles");
    }

    public function rolesEditAction($id)
    {
        $item = Role::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/users/roles");


        $form = new Form_Admin_Users_RoleEdit($item);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();
        if ($item->getIsDefault()){
            $this->db->update(
                $item->getSource(),
                array('is_default'),
                array(0),
                "id != {$item->getId()}"
            );
        }

        $this->response->redirect("admin/users/roles");
    }

    public function rolesDeleteAction($id)
    {
        $item = Role::findFirst($id);
        if ($item){
            if ($item->getIsDefault()){
                $anotherRole = Role::findFirst();
                $anotherRole->setIsDefault(1);
                $anotherRole->save();
            }
            $item->delete();
        }

        return $this->response->redirect("admin/users/roles");
    }
}

