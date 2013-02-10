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

        $pages = Page::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $pages,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

    public function createAction()
    {
        $form = new Form_Admin_Pages_Create();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $this->response->redirect("admin/pages/manage?id=" . $form->getData()->getId());
    }

    public function editAction($id)
    {
        $page = Page::findFirst($id);
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
        $page = Page::findFirst($id);
        if ($page)
            $page->delete();

        return $this->response->redirect("admin/pages");
    }


    public function manageAction($id)
    {
        $page = Page::findFirst($id);
        if (!$page)
            return $this->response->redirect("admin/pages");

        // Collecting widgets info
        $content = Widget::find();
        $modules = array(null => "Core");
        $bundlesWidgetsMetadata = array();
        foreach ($content as $widget) {
            $bundlesWidgetsMetadata[$modules[$widget->getModuleId()]][$widget->getId()] = array(
                'widget_id' => $widget->getId(),
                'title' => $widget->getTitle(),
                'description' => $widget->getDescription(),
                'adminAction' => $widget->getAdminForm(),
                'name' => $widget->getName()
            );
        }

        //Creating Widgets List data
        $widgetsListData = array();
        foreach ($bundlesWidgetsMetadata as $key => $widgetsMeta) {
            foreach ($widgetsMeta as $wId => $wMeta) {
                $widgetsListData[$wId] = $wMeta;
                $widgetsListData[$wId]["name"] = $widgetsMeta[$wId]["name"] = $wMeta['name'];
                $widgetsListData[$wId]["widget_id"] = $widgetsMeta[$wId]["widget_id"] = $wId;

            }
            $bundlesWidgetsMetadata[$key] = $widgetsMeta;
        }

        $content = $page->getWidgets(false);
        $currentPageWidgets = array();
        foreach ($content as $widget)
            $currentPageWidgets[] = array(
                'id' => $widget->getId(),
                'layout' => $widget->getLayout(),
                'widget_id' => $widget->getWidgetId(),
                'params' => $widget->getParams()
            );


        $this->view->setVar('currentPage', $page);
        $this->view->setVar('bundlesWidgetsMetadata', json_encode($bundlesWidgetsMetadata));
        $this->view->setVar('widgetsListData', json_encode($widgetsListData));
        $this->view->setVar('currentPageWidgets', json_encode($currentPageWidgets));
    }

    public function widgetOptionsAction()
    {
        $id = $this->request->get('id', 'int', 0);
        $widgetParams = $this->request->get('params');
        $widget_id = $this->request->get('widget_id');
        $page_id = $this->request->get('page_id');
        $widgetMetadata = Widget::findFirst('id = ' . $widget_id);
        $form = new Form();

        // building widget form
        $adminForm = $widgetMetadata->getAdminForm();
        if (empty($adminForm)) {
            $form->addElement('textField', 'title', array(
                'label' => $this->di->get('trans')->_('Title')
            ));

            if ($widgetMetadata->getIsPaginated() == 1) {
                $form->addElement('textField', 'count', array(
                    'label' => $this->di->get('trans')->_('Items count'),
                    'value' => 10
                ));
            }
        } elseif ($adminForm == 'action') {
            $widgetName = $widgetMetadata->getName();
            $widgetClass = "Widget_{$widgetName}_Controller";
            $widgetObject = new $widgetClass();
            $widgetObject->initialize();
            $form = call_user_func_array(array($widgetObject, "adminAction"), $_REQUEST);
        } else {
            $form = new $adminForm();
        }

        // set form values
        if (!empty($widgetParams))
            $form->setData($widgetParams);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            $this->view->setVar('form', $form);
            $this->view->setVar('id', $id);
            $this->view->setVar('name', $widgetMetadata->getName());
            $this->view->setVar('page_id', $page_id);

            return;
        }


        $d = json_encode($form->getData());
        $this->view->setVar('params', json_encode($form->getData()));


        $this->view->setVar('form', $form);
        $this->view->setVar('id', $id);
        $this->view->setVar('name', $widgetMetadata->getName());
        $this->view->setVar('page_id', $page_id);
    }

    public function saveLayoutAction($id)
    {
        $response = new Phalcon\Http\Response();
        $response->setStatusCode(200, "OK");
        $response->setContent(json_encode(array("error" => 0)));

        $layout = $this->request->get("layout");
        $items = $this->request->get("items");

        $page = Page::findFirst($id);
        $page->setLayout($layout);
        $page->setWidgets($items);
        $page->save();

        return $response->send();
    }

    public function suggestAction(){
        $this->view->disable();
        $query = $this->request->get('query');
        if (!$query){
            $this->response->setContent('[]')->send();
            return;
        }


        $results = Page::find(
            array(
                "conditions" => "title LIKE ?1",
                "bind"       => array(1 => '%'.$query.'%')
            )
        );

        $data = array();
        foreach($results as $result){
            $data[] = array(
                'id' => $result->getId(),
                'label' => $result->getTitle()
            );
        }

        $this->response->setContent(json_encode($data))->send();
    }

}

