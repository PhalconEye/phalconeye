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

namespace Core\Helper;

use Core\Model\PageModel;
use Engine\Exception;
use Engine\Helper\AbstractHelper;
use Engine\Widget\Element;
use Phalcon\Db\Column;
use Phalcon\Mvc\View;
use User\Model\UserModel;

/**
 * Content renderer.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RendererHelper extends AbstractHelper
{
    /**
     * Render content from database layout.
     *
     * @param string $pageType Page type.
     * @param string $layout   Use layout to render.
     *
     * @throws \Engine\Exception
     * @return mixed
     */
    public function renderContent($pageType, $layout = null)
    {
        $content = '';
        $page = PageModel::findFirst(
            [
                'conditions' => 'type=:type:',
                'bind' => ["type" => $pageType],
                'bindTypes' => ["type" => Column::BIND_PARAM_STR]
            ]
        );

        if (!$page) {
            throw new Exception("Page with type '$pageType' not found.");
        }

        $widgets = $page->getWidgets();

        /**
         * Plain render widgets.
         */
        if (!$layout) {
            foreach ($widgets as $widget) {
                $content .= $this->renderWidgetId($widget->widget_id, $widget->getParams());
            }

            return $content;
        }

        // Resort content by sides.
        $content = [];
        foreach ($widgets as $widget) {
            $content[$widget->layout][] = $this->renderWidgetId($widget->widget_id, $widget->getParams());
        }

        /** @var \Phalcon\Mvc\View $view */
        $view = $this
            ->getDI()
            ->getView()
            ->reset()
            ->setVars([], false)
            ->disableLevel(View::LEVEL_LAYOUT)
            ->disableLevel(View::LEVEL_MAIN_LAYOUT);

        $view->content = $content;
        $view->page = $page;
        $view->pick($layout);
        $view->getRender(null, null);
        return $view->getContent();
    }

    /**
     * Render widget.
     *
     * @param mixed  $id     Widget id in widgets table.
     * @param array  $params Widgets params in page.
     * @param string $action Widgets action to render.
     *
     * @return mixed
     */
    public function renderWidget($id, $params = [], $action = 'index')
    {
        if (!$this->widgetIsAllowed($params)) {
            return '';
        }
        $widget = new Element($id, $params, $this->getDI());

        return $widget->render($action);
    }

    /**
     * Render widget by identity.
     *
     * @param mixed  $id     Widget id in widgets table.
     * @param array  $params Widgets params in page.
     * @param string $action Widgets action to render.
     *
     * @return mixed
     */
    public function renderWidgetId($id, $params = [], $action = 'index')
    {
        return $this->renderWidget((int)$id, $params, $action);
    }

    /**
     * Check that this widget is allowed for current user.
     *
     * @param array $params User params.
     *
     * @return bool
     */
    public function widgetIsAllowed($params)
    {
        $viewer = UserModel::getViewer();
        if (empty($params['roles']) || !is_array($params['roles'])) {
            return true;
        }

        return in_array($viewer->role_id, $params['roles']);
    }
}