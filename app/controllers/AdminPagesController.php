<?php

class AdminPagesController extends Controller
{
    public function init()
    {
        $navigation = new Navigation($this->di);
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'index' => array(
                'href' => 'admin/pages',
                'title' => 'Browse'
            ),
            'create' => array(
                'href' => 'admin/pages/create',
                'title' => 'Create new page'
            )))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigation', $navigation);
    }

    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $pages = Pages::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $pages,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('page', $page);
    }

    public function createAction()
    {
        $form = new Form_Admin_Pages_Create();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $form->getData()->save();
        $this->response->redirect("admin/pages/manage?id=" . $form->getData()->getId());
    }

    public function editAction($id)
    {
        $page = Pages::findFirst($id);
        if (!$page)
            return $this->response->redirect("admin/pages");


        $form = new Form_Admin_Pages_Edit($page);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $this->response->redirect("admin/pages");
    }

    public function deleteAction($id)
    {
        $page = Pages::findFirst($id);
        if (!$page)
            return $this->response->redirect("admin/pages");

        $page->delete();

        return $this->response->redirect("admin/pages");
    }


    public function manageAction($id)
    {
        $page = Pages::findFirst($id);
        if (!$page)
            return $this->response->redirect("admin/pages");

        // Collecting widgets infor
        $widgets = Widgets::find();
        $modules = array(null => "Core");
        $bundlesWidgetsMetadata = array();
        foreach ($widgets as $widget) {
            $bundlesWidgetsMetadata[$modules[$widget->getModuleId()]][$widget->getName()] = array(
                'title' => $widget->getTitle(),
                'description' => $widget->getDescription(),
                'adminAction' => $widget->getAdminForm(),
                'name' => $widget->getName()
            );
        }

        //Creating Widgets List data
        $widgetsListData = array();
        foreach ($bundlesWidgetsMetadata as $key => $widgetsMeta) {
            foreach ($widgetsMeta as $wName => $wMeta) {
                $widgetsListData[$wName] = $wMeta;
                $widgetsListData[$wName]["name"] = $widgetsMeta[$wName]["name"] = $wName;

            }
            $bundlesWidgetsMetadata[$key] = $widgetsMeta;
        }

        $currentPageWidgets = array();

        $this->view->setVar('currentPage', $page);
        $this->view->setVar('bundlesWidgetsMetadata', json_encode($bundlesWidgetsMetadata));
        $this->view->setVar('widgetsListData', json_encode($widgetsListData));
        $this->view->setVar('currentPageWidgets', json_encode($currentPageWidgets));
    }

    public function optionsAction(){
        $this->view->setVar('test', 'main');
    }

}

