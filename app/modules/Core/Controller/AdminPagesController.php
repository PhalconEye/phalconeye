<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

use Core\Model\Page;
use Engine\Form;
use Engine\Navigation;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Paginator\Adapter\QueryBuilder;
use \Core\Form\Admin\Page\Create as CreateForm;
use Core\Form\Admin\Page\Edit as EditForm;

/**
 * Admin pages.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/pages", name="admin-pages")
 */
class AdminPagesController extends BaseAdmin
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
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/pages',
                    'title' => 'Browse',
                    'prepend' => '<i class="icon-list icon-white"></i>'
                ),
                1 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'create' => array(
                    'href' => 'admin/pages/create',
                    'title' => 'Create new page',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                )));

        $this->view->navigation = $navigation;
    }

    /**
     * Controller index.
     *
     * @return void
     *
     * @Get("/", name="admin-pages")
     */
    public function indexAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->from('\Core\Model\Page');

        $paginator = new QueryBuilder(
            array(
                "builder" => $builder,
                "limit" => 25,
                "page" => $this->request->getQuery('page', 'int', 1)
            )
        );

        // Get the paginated results.
        $this->view->paginator = $paginator->getPaginate();
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

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $page = $form->getValues();
        $url = $page->url;
        if (!empty($url)) {
            $page->url = str_replace('/', '', str_replace('\\', '', $url));
        }

        $page->save();
        $this->flashSession->success('New object created successfully!');

        return $this->response->redirect(array('for' => "admin-pages-manage", 'id' => $form->getValues()->id));
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

        if (!$page) {
            return $this->response->redirect(array('for' => "admin-pages"));
        }

        $form = new EditForm($page);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $page = $form->getValues();
        $url = $page->url;
        if (!empty($url) && $url != '/') {
            $page->url = str_replace('/', '', str_replace('\\', '', $url));
        }

        $roles = $this->request->get('roles');
        if ($roles == null) {
            $page->roles = array();
        }

        $page->save();
        $this->flashSession->success('Object saved!');

        return $this->response->redirect(array('for' => "admin-pages"));
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

        return $this->response->redirect(array('for' => "admin-pages"));
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

            return $this->response->redirect(array('for' => "admin-pages"));
        }

        // Collecting widgets info.
        $query = $this->modelsManager->createBuilder()
            ->from(array('t' => '\Core\Model\Widget'))
            ->where("t.enabled = :enabled:", array('enabled' => 1));
        $widgets = $query->getQuery()->execute();

        $modulesDefinition = $this->getDI()->get('modules');
        $modules = array();
        foreach ($modulesDefinition as $module => $enabled) {
            if (!$enabled) {
                continue;
            }
            $modules[$module] = ucfirst($module);
        }
        $bundlesWidgetsMetadata = array();
        foreach ($widgets as $widget) {
            $moduleName = $widget->module;
            if (!$moduleName) {
                $moduleName = 'Other';
            } else {
                $moduleName = $modules[$moduleName];
            }
            $bundlesWidgetsMetadata[$moduleName][$widget->id] = array(
                'widget_id' => $widget->id,
                'description' => $widget->description,
                'name' => $widget->name
            );
        }

        // Creating Widgets List data.
        $widgetsListData = array();
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
        $currentPageWidgets = array();
        $widgetIndex = 0;
        foreach ($content as $widget) {
            $currentPageWidgets[$widgetIndex] = array(
                'widget_index' => $widgetIndex, // Identification for this array.
                'id' => $widget->id,
                'layout' => $widget->layout,
                'widget_id' => $widget->widget_id,
                'params' => $widget->getParams()
            );
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
        $currentPageWidgets = $this->session->get('admin-pages-manage', array());

        if ($widgetIndex == -1) {
            $widgetIndex = $this->session->get('admin-pages-widget-index');
            $currentPageWidgets[$widgetIndex] = array(
                'widget_index' => $widgetIndex, // indification for this array
                'id' => 0,
                'layout' => $this->request->get('layout', 'string', 'middle'),
                'widget_id' => $this->request->get('widget_id', 'int'),
                'params' => array()
            );

        }

        if (empty($currentPageWidgets[$widgetIndex])) {
            return;
        }

        $widgetData = $currentPageWidgets[$widgetIndex];

        $id = $widgetData['id'];
        $widgetParams = $widgetData['params'];
        $widget_id = $widgetData['widget_id'];
        $widgetMetadata = Widget::findFirstById($widget_id);
        $form = new Form();

        // building widget form
        $adminForm = $widgetMetadata->admin_form;
        if (empty($adminForm)) {
            $form->addElement('text', 'title', array(
                'label' => 'Title'
            ));
        } elseif ($adminForm == 'action') {
            $widgetName = $widgetMetadata->name;
            if ($widgetMetadata->module !== null) {
                $widgetClass = '\\' . ucfirst($widgetMetadata->module) . '\Widget\\' . $widgetName . '\Controller';
            } else {
                $widgetClass = '\Widget\\' . $widgetName . '\Controller';
            }
            $widgetObject = new $widgetClass();
            $widgetObject->start();
            $form = call_user_func_array(array($widgetObject, "adminAction"), $_REQUEST);
        } else {
            $form = new $adminForm();
        }

        if ($widgetMetadata->is_paginated == 1) {
            $form->addElement('text', 'count', array(
                'label' => 'Items count',
                'value' => 10
            ), 10000);
        }

        if ($widgetMetadata->is_acl_controlled == 1) {
            $form->addElement('select', 'roles', array(
                'label' => 'Roles',
                'options' => \User\Model\Role::find(),
                'using' => array('id', 'name'),
                'multiple' => 'multiple'
            ), 10000);

        }

        // set form values
        if (!empty($widgetParams)) {
            $form->setValues($widgetParams);
        }

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            $this->view->id = $id;
            $this->view->name = $widgetMetadata->name;

            return;
        }

        $currentPageWidgets[$widgetIndex]['params'] = $form->getValues();
        $this->view->widget_index = $widgetIndex;

        $this->view->form = $form;
        $this->view->id = $id;
        $this->view->name = $widgetMetadata->name;

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
        $response->setContent(json_encode(array("error" => 0)));

        $layout = $this->request->get("layout");
        $items = $this->request->get("items");

        $page = Page::findFirstById($id);
        $page->layout = $layout;
        $page->setWidgets($items);
        $page->save();

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
            array(
                "conditions" => "title LIKE ?1",
                "bind" => array(1 => '%' . $query . '%')
            )
        );

        $data = array();
        foreach ($results as $result) {
            $data[] = array(
                'id' => $result->id,
                'label' => $result->title
            );
        }

        $this->response->setContent(json_encode($data))->send();
    }

}

