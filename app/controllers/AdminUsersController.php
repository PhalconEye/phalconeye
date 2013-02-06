<?php

class AdminUsersController extends Controller
{
    public function init()
    {
        $navigation = new Navigation($this->di);
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'index' => array(
                'href' => 'admin/users',
                'title' => 'Browse'
            ),
            'create' => array(
                'href' => 'admin/users/create',
                'title' => 'Create new user'
            )))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigation', $navigation);
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

        $this->view->setVar('page', $page);
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
}

