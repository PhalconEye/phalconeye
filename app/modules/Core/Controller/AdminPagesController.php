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

namespace Core\Controller;

use Core\Controller\Grid\Admin\PageGrid;
use Core\Form\Admin\Page\Create as CreateForm;
use Core\Form\Admin\Page\Edit as EditForm;
use Core\Form\CoreForm;
use Core\Model\Page;
use Core\Model\Widget;
use Engine\Navigation;
use Engine\Widget\Controller as WidgetController;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use User\Model\Role;

/**
 * Admin pages.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/pages", name="admin-pages")
 */
class AdminPagesController extends AbstractAdminController
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
                        'href' => 'admin/pages',
                        'title' => 'Browse',
                        'prepend' => '<i class="glyphicon glyphicon-list"></i>'
                    ],
                    1 => [
                        'href' => 'javascript:;',
                        'title' => '|'
                    ],
                    'create' => [
                        'href' => 'admin/pages/create',
                        'title' => 'Create new page',
                        'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                    ]
                ]
            );

        $this->view->navigation = $navigation;
    }

    /**
     * Controller index.
     *
     * @return void|ResponseInterface
     *
     * @Get("/", name="admin-pages")
     */
    public function indexAction()
    {
        $grid = new PageGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Create new page.
     *
     * @return mixed
     *
     * @Route("/create", methods={"GET", "POST"}, name="admin-pages-create")
     */
    public function createAction()
    {
        $form = new CreateForm();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $page = $form->getEntity();
        if (!empty($page->url)) {
            $page->url = str_replace('/', '', str_replace('\\', '', $page->url));
        }

        $page->save();
        $this->flashSession->success('New object created successfully!');

        return $this->response->redirect(['for' => "admin-pages-manage", 'id' => $page->id]);
    }

    /**
     * Edit page.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-pages-edit")
     */
    public function editAction($id)
    {
        $page = Page::findFirstById($id);

        if (!$page || (!empty($page->type) && $page->type != Page::PAGE_TYPE_HOME)) {
            $this->flashSession->notice('Nothing to edit!');
            return $this->response->redirect(['for' => "admin-pages"]);
        }

        $form = new EditForm($page);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $page = $form->getEntity();
        if (!empty($page->url) && $page->url != '/') {
            $page->url = str_replace('/', '', str_replace('\\', '', $page->url));
        }

        $page->save();
        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => "admin-pages"]);
    }

    /**
     * Delete page.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Get("/delete/{id:[0-9]+}", name="admin-pages-delete")
     */
    public function deleteAction($id)
    {
        $page = Page::findFirstById($id);
        if ($page) {
            $page->delete();
            $this->flashSession->notice('Object deleted!');
        }

        return $this->response->redirect(['for' => "admin-pages"]);
    }

    /**
     * Manage page content.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Get("/manage/{id:[0-9]+}", name="admin-pages-manage")
     */
    public function manageAction($id)
    {
        $this->view->headerNavigation->setActiveItem('admin/pages');
        $page = Page::findFirstById($id);
        if (!$page) {
            $this->flashSession->notice('Page not found!');

            return $this->response->redirect(['for' => "admin-pages"]);
        }

        // Collecting widgets info.
        $query = $this->modelsManager->createBuilder()
            ->from(['t' => '\Core\Model\Widget'])
            ->where("t.enabled = :enabled:", ['enabled' => 1]);
        $widgets = $query->getQuery()->execute();

        $modulesDefinition = $this->getDI()->get('registry')->modules;
        $modules = [];
        foreach ($modulesDefinition as $module) {
            $modules[$module] = ucfirst($module);
        }
        $bundlesWidgetsMetadata = [];
        foreach ($widgets as $widget) {
            $moduleName = $widget->module;
            if (!$moduleName || empty($modules[$moduleName])) {
                $moduleName = 'Other';
            } else {
                $moduleName = $modules[$moduleName];
            }
            $bundlesWidgetsMetadata[$moduleName][$widget->id] = [
                'widget_id' => $widget->id,
                'description' => $widget->description ? $widget->description : '',
                'name' => $widget->name
            ];
        }

        // Creating Widgets List data.
        $widgetsListData = [];
        foreach ($bundlesWidgetsMetadata as $key => $widgetsMeta) {
            foreach ($widgetsMeta as $wId => $wMeta) {
                $widgetsListData[$wId] = $wMeta;
                $widgetsListData[$wId]["name"] = $widgetsMeta[$wId]["name"] = $wMeta['name'];
                $widgetsListData[$wId]["widget_id"] = $widgetsMeta[$wId]["widget_id"] = $wId;
                unset($widgetsListData[$wId]['adminAction']); // this throw exception in parseJSON

            }
            $bundlesWidgetsMetadata[$key] = $widgetsMeta;
        }

        $content = $page->getWidgets();
        $currentPageWidgets = [];
        $widgetIndex = 0;
        foreach ($content as $widget) {
            $currentPageWidgets[$widgetIndex] = [
                'widget_index' => $widgetIndex, // Identification for this array.
                'id' => $widget->id,
                'layout' => $widget->layout,
                'widget_id' => $widget->widget_id,
                'params' => $widget->getParams()
            ];
            $widgetIndex++;
        }


        // Store parameters in session.
        $this->session->set('admin-pages-manage', $currentPageWidgets);
        $this->session->set('admin-pages-widget-index', $widgetIndex);

        $this->view->currentPage = $page;
        $this->view->bundlesWidgetsMetadata = json_encode($bundlesWidgetsMetadata);
        $this->view->widgetsListData = json_encode($widgetsListData);
        $this->view->currentPageWidgets = json_encode($currentPageWidgets);
    }

    /**
     * Widget options.
     *
     * @return void
     *
     * @Route("/widget-options", methods={"GET", "POST"}, name="admin-pages-widget-options")
     */
    public function widgetOptionsAction()
    {
        $widgetIndex = $this->request->get('widget_index', 'int', -1);
        if ($widgetIndex != '0' && intval($widgetIndex) == 0) {
            $widgetIndex = -1;
        }
        $currentPageWidgets = $this->session->get('admin-pages-manage', []);

        if ($widgetIndex == -1) {
            $widgetIndex = $this->session->get('admin-pages-widget-index');
            $currentPageWidgets[$widgetIndex] = [
                'widget_index' => $widgetIndex, // identification for this array.
                'id' => 0,
                'layout' => $this->request->get('layout', 'string', 'middle'),
                'widget_id' => $this->request->get('widget_id', 'int'),
                'params' => []
            ];
        }

        if (empty($currentPageWidgets[$widgetIndex])) {
            return;
        }

        $widgetData = $currentPageWidgets[$widgetIndex];

        $id = $widgetData['id'];
        $widgetParams = $widgetData['params'];
        $widgetParams['content_id'] = $id;
        $widget_id = $widgetData['widget_id'];
        $widgetMetadata = Widget::findFirstById($widget_id);
        $form = new CoreForm();

        // building widget form
        $adminForm = $widgetMetadata->admin_form;
        if (empty($adminForm)) {
            $form->addText('title');
        } elseif ($adminForm == 'action') {
            $widgetName = $widgetMetadata->name;
            if ($widgetMetadata->module !== null) {
                $widgetClass = '\\' . ucfirst($widgetMetadata->module) . '\Widget\\' . $widgetName . '\Controller';
            } else {
                $widgetClass = '\Widget\\' . $widgetName . '\Controller';
            }
            $widgetController = new $widgetClass();
            $widgetController->setDefaults($widgetName, ucfirst($widgetMetadata->module), $widgetParams);
            $widgetController->prepare();
            $form = $widgetController->adminAction();
        } else {
            $form = new $adminForm();
        }

        if ($widgetMetadata->is_paginated == 1) {
            $form->addText('count', 'Items count', null, 10);
            $form->setOrder('count', 1000);
        }

        if ($widgetMetadata->is_acl_controlled == 1) {
            $form->addMultiSelect(
                'roles',
                'Roles',
                null,
                Role::find(),
                null,
                ['using' => ['id', 'name']]
            );
            $form->setOrder('roles[]', 1001);
        }

        // set form values
        if (!empty($widgetParams)) {
            $form->setValues($widgetParams);
        }

        if (!$this->request->isPost() || !$form->isValid()) {
            $this->view->form = $form;
            $this->view->id = $id;
            $this->view->name = $widgetMetadata->name;

            return;
        }

        $currentPageWidgets[$widgetIndex]['params'] = $form->getValues();

        $this->resolveModal(
            [
                'hide' => true,
                'customJs' => 'setEditedWidgetIndex(' . $widgetIndex . ');'
            ]
        );

        $this->session->set('admin-pages-manage', $currentPageWidgets);
        $this->session->set('admin-pages-widget-index', ++$widgetIndex);
    }

    /**
     * Save page layout with content.
     *
     * @param int $id Page identity.
     *
     * @return ResponseInterface
     *
     * @Route("/save-layout/{id:[0-9]+}", methods={"POST"}, name="admin-pages-save-layout")
     */
    public function saveLayoutAction($id)
    {
        $response = new Response();
        $response->setStatusCode(200, "OK");
        $response->setContent(json_encode(["error" => 0]));

        $layout = $this->request->get("layout");
        $items = $this->request->get("items");

        // Save page with widgets and layout.
        $page = Page::findFirstById($id);
        $page->layout = $layout;
        $page->setWidgets($items);
        $page->save();

        // Clear widgets cache.
        /** @var \Phalcon\Cache\BackendInterface $cache */
        $cache = $this->getDI()->get('cacheOutput');
        $prefix = $this->config->application->cache->prefix;
        $widgetKeys = $cache->queryKeys($prefix . WidgetController::CACHE_PREFIX);
        foreach ($widgetKeys as $key) {
            $cache->delete(str_replace($prefix, '', $key));
        }

        $this->flashSession->success('Page saved!');

        return $response->send();
    }

    /**
     * Suggest page.
     *
     * @return void
     *
     * @Route("/suggest", methods={"GET"}, name="admin-pages-suggest")
     */
    public function suggestAction()
    {
        $this->view->disable();
        $query = $this->request->get('query');
        if (empty($query)) {
            $this->response->setContent('[]')->send();

            return;
        }

        $results = Page::find(
            [
                "conditions" => "title LIKE ?1",
                "bind" => [1 => '%' . $query . '%']
            ]
        );

        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result->id,
                'label' => $result->title
            ];
        }

        $this->response->setContent(json_encode($data))->send();
    }

}

