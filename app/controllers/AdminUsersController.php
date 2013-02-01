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

        $users = Users::find();

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

    }
}

