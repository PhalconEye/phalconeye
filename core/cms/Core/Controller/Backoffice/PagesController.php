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

namespace Core\Controller\Backoffice;

use Core\Grid\Backoffice\PageGrid;
use Core\Form\Backoffice\Page\PageCreateForm;
use Core\Form\Backoffice\Page\PageEditForm;
use Core\Form\CoreForm;
use Core\Model\PageModel;
use Core\Navigation\Backoffice\PagesNavigation;
use Engine\Widget\Controller as WidgetController;
use Engine\Widget\WidgetData;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use User\Model\RoleModel;

/**
 * Admin pages.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/pages", name="backoffice-pages")
 */
class PagesController extends AbstractBackofficeController
{
    /**
     * Init navigation.
     *
     * @return void
     */
    public function init()
    {
        $this->view->navigation = new PagesNavigation();
    }

    /**
     * Controller index.
     *
     * @return void|ResponseInterface
     *
     * @Get("/", name="backoffice-pages")
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
     * @Route("/create", methods={"GET", "POST"}, name="backoffice-pages-create")
     */
    public function createAction()
    {
        $form = new PageCreateForm();
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

        return $this->response->redirect(['for' => "backoffice-pages-manage", 'id' => $page->id]);
    }

    /**
     * Edit page.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-pages-edit")
     */
    public function editAction($id)
    {
        $page = PageModel::findFirstById($id);

        if (!$page || (!empty($page->type) && $page->type != PageModel::PAGE_TYPE_HOME)) {
            $this->flashSession->notice('Nothing to edit!');
            return $this->response->redirect(['for' => "backoffice-pages"]);
        }

        $form = new PageEditForm($page);
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

        return $this->response->redirect(['for' => "backoffice-pages"]);
    }

    /**
     * Delete page.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Get("/delete/{id:[0-9]+}", name="backoffice-pages-delete")
     */
    public function deleteAction($id)
    {
        $page = PageModel::findFirstById($id);
        if ($page) {
            $page->delete();
            $this->flashSession->notice('Object deleted!');
        }

        return $this->response->redirect(['for' => "backoffice-pages"]);
    }

    /**
     * Manage page content.
     *
     * @param int $id Page identity.
     *
     * @return mixed
     *
     * @Get("/manage/{id:[0-9]+}", name="backoffice-pages-manage")
     */
    public function manageAction($id)
    {
        $page = PageModel::findFirstById($id);
        if (!$page) {
            $this->flashSession->notice('Page not found!');

            return $this->response->redirect(['for' => "backoffice-pages"]);
        }

        $widgets = $this->getDI()->getWidgets()->getAll();
        $modules = array_keys($this->getDI()->getRegistry()->modules);
        $bundlesWidgetsMetadata = [];
        /** @var WidgetData $widget */
        foreach ($widgets as $code => $widget) {
            $moduleName = $widget->getModule();
            if (!in_array($moduleName, $modules)) {
                $moduleName = '_External_';
            } else {
                $moduleName = ucfirst($moduleName);
            }
            $bundlesWidgetsMetadata[$moduleName][$code] = [
                'widget_code' => $code,
                'name' => $widget->getName(),
                'description' => $widget->getMetadata(WidgetData::METADATA_DESCRIPTION),
            ];
        }

        // Creating Widgets List data.
        $widgetsListData = [];
        foreach ($bundlesWidgetsMetadata as $key => $widgetsMeta) {
            foreach ($widgetsMeta as $code => $meta) {
                $widgetsListData[$code] = $meta;
                $widgetsListData[$code]["name"] = $widgetsMeta[$code]["name"] = $meta['name'];
                $widgetsListData[$code]["code"] = $widgetsMeta[$code]["code"] = $code;
                unset($widgetsListData[$code]['adminAction']); // this throw exception in parseJSON

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
                'widget_code' => $widget->widget_code,
                'params' => $widget->getParams()
            ];
            $widgetIndex++;
        }


        // Store parameters in session.
        $this->session->set('backoffice-pages-manage', $currentPageWidgets);
        $this->session->set('backoffice-pages-widget-index', $widgetIndex);

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
     * @Route("/widget-options", methods={"GET", "POST"}, name="backoffice-pages-widget-options")
     */
    public function widgetOptionsAction()
    {
        $widgetIndex = $this->request->get('widget_index', 'int', -1);
        if ($widgetIndex != '0' && intval($widgetIndex) == 0) {
            $widgetIndex = -1;
        }
        $currentPageWidgets = $this->session->get('backoffice-pages-manage', []);

        if ($widgetIndex == -1) {
            $widgetIndex = $this->session->get('backoffice-pages-widget-index');
            $currentPageWidgets[$widgetIndex] = [
                'widget_index' => $widgetIndex, // identification for this array.
                'id' => 0,
                'layout' => $this->request->get('layout', 'string', 'middle'),
                'widget_code' => $this->request->get('widget_code', 'string'),
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
        $widgetCode = $widgetData['widget_code'];
        $widgetMetadata = $this->getDI()->getWidgets()->get($widgetCode);
        $form = new CoreForm();

        // Get widget controller class.
        $widgetName = $widgetMetadata->getName();
        $widgetModuleName = '';
        if ($widgetMetadata->getModule() !== null) {
            $widgetModuleName = ucfirst($widgetMetadata->getModule());
            $widgetClass = '\\' . $widgetModuleName . '\Widget\\' . $widgetName . '\Controller';
        } else {
            $widgetClass = '\Widget\\' . $widgetName . '\Controller';
        }

        /** @var WidgetController $widgetController */
        $widgetController = new $widgetClass();
        $widgetController->setDefaults($widgetName, $widgetModuleName, $widgetParams);

        // building widget form
        $adminForm = $widgetController->getAdminForm();
        if (empty($adminForm)) {
            $form->addText('title');
        } elseif (is_string($adminForm)) {
            $widgetController->prepare();
            $form = $widgetController->{$adminForm . 'Action'}();
        } else {
            $form = $adminForm;
        }

        if ($widgetController->isPaginated() == 1) {
            $form->addText('count', 'Items count', null, 10);
            $form->setOrder('count', 1000);
        }

        if ($widgetController->isAclControlled()) {
            $form->addMultiSelect(
                'roles',
                'Roles',
                null,
                RoleModel::find(),
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
            $this->view->name = $widgetMetadata->getName();
            $this->view->setIsBackoffice(true);

            return;
        }

        $currentPageWidgets[$widgetIndex]['params'] = $form->getValues();

        $this->resolveModal(
            [
                'hide' => true,
                'customJs' => 'setEditedWidgetIndex(' . $widgetIndex . ');'
            ]
        );

        $this->session->set('backoffice-pages-manage', $currentPageWidgets);
        $this->session->set('backoffice-pages-widget-index', ++$widgetIndex);
    }

    /**
     * Save page layout with content.
     *
     * @param int $id Page identity.
     *
     * @return ResponseInterface
     *
     * @Route("/save-layout/{id:[0-9]+}", methods={"POST"}, name="backoffice-pages-save-layout")
     */
    public function saveLayoutAction($id)
    {
        $response = new Response();
        $response->setStatusCode(200, "OK");
        $response->setContent(json_encode(["error" => 0]));

        $layout = $this->request->get("layout");
        $items = $this->request->get("items");

        // Save page with widgets and layout.
        $page = PageModel::findFirstById($id);
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
     * @Route("/suggest", methods={"GET"}, name="backoffice-pages-suggest")
     */
    public function suggestAction()
    {
        $this->view->disable();
        $query = $this->request->get('query');
        if (empty($query)) {
            $this->response->setContent('[]')->send();

            return;
        }

        $results = PageModel::find(
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

