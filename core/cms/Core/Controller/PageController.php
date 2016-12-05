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
  +------------------------------------------------------------------------+
*/

namespace Core\Controller;

use Core\Helper\RendererHelper;
use Core\Model\PageModel;
use Engine\Application;
use Phalcon\Db\Column;

/**
 * Page selection controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/page", name="page")
 */
class PageController extends AbstractController
{
    const
        CACHE_KEY_PAGE = 'page_';

    /**
     * Index action.
     *
     * @param string $url Url path.
     *
     * @return void
     *
     * @Route("/{url:[a-zA-Z0-9_-]+}", methods={"GET", "POST"}, name="page")
     */
    public function indexAction($url)
    {
        $this->renderPage($url);
    }

    /**
     * Render some content from database layout.
     *
     * @param null|string $url        Url definition of page.
     * @param null|string $controller Related controller name.
     * @param null|string $type       Page type.
     *
     * @return mixed
     */
    public function renderPage($url = null, $controller = null, $type = null)
    {
        $page = $this->getPage($url, $controller, $type);
        if (!$page || !$page->isAllowed()) {
            $this->dispatcher->forward(
                [
                    'controller' => 'Error',
                    'action' => 'show404'
                ]
            );

            return;
        }

        // Resort content by sides.
        if ($page->use_dynamic_layout) {
            $content = [];
        } else {
            $content = ['top' => [], 'left' => [], 'middle' => [], 'right' => [], 'bottom' => []];
        }

        $renderer = RendererHelper::getInstance($this->getDI());
        foreach ($page->getWidgets() as $widget) {
            $content[$widget->layout][] = $renderer->renderWidgetId($widget->widget_code, $widget->getParams());
        }

        $this->renderParts($renderer);
        $this->view->content = $content;
        $this->view->page = $page;
        $this->view->pick('layouts/page', Application::CMS_MODULE_CORE);
    }

    /**
     * Get page model by parameters.
     *
     * @param null|string $url        Url definition of page.
     * @param null|string $controller Related controller name.
     * @param null|string $type       Page type.
     *
     * @return PageModel|null
     */
    public function getPage($url = null, $controller = null, $type = null) : PageModel
    {
        /** @var PageModel $page */
        $page = null;
        if ($url !== null) {
            $page = PageModel::find(
                [
                    'conditions' => 'url=:url1: OR url=:url2: OR id = :url3:',
                    'bind' => ["url1" => $url, "url2" => '/' . $url, "url3" => $url],
                    'bindTypes' => [
                        "url1" => Column::BIND_PARAM_STR,
                        "url2" => Column::BIND_PARAM_STR,
                        "url3" => Column::BIND_PARAM_INT
                    ]
                ]
            )->getFirst();

        } elseif ($controller !== null) {
            $page = PageModel::find(
                [
                    'conditions' => 'controller=:controller:',
                    'bind' => ["controller" => $controller],
                    'bindTypes' => ["controller" => Column::BIND_PARAM_STR]
                ]
            )->getFirst();
        } elseif ($type !== null) {
            $page = PageModel::find(
                [
                    'conditions' => 'type=:type:',
                    'bind' => ["type" => $type],
                    'bindTypes' => ["type" => Column::BIND_PARAM_STR]
                ]
            )->getFirst();
        }

        return $page;
    }
}
