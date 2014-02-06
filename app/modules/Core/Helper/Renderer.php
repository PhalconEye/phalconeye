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

namespace Core\Helper;

use Core\Model\Page;
use Engine\Helper;
use Engine\Widget\Element;
use Phalcon\Db\Column;
use Phalcon\Tag;
use User\Model\User;

/**
 * Content renderer.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Renderer extends Helper
{
    /**
     * Render content from database layout.
     *
     * @param string $pageType Page type.
     *
     * @return mixed
     */
    protected function _renderContent($pageType)
    {
        $content = '';
        $page = Page::findFirst(
            [
                'conditions' => 'type=:type:',
                'bind' => ["type" => $pageType],
                'bindTypes' => ["type" => Column::BIND_PARAM_STR]
            ]
        );

        $widgets = $page->getWidgets();
        foreach ($widgets as $widget) {
            $content .= $this->_renderWidgetId($widget->widget_id, $widget->getParams());
        }

        return $content;
    }

    /**
     * Render widget.
     *
     * @param mixed $id     Widget id in widgets table.
     * @param array $params Widgets params in page.
     *
     * @return mixed
     */
    protected function _renderWidget($id, $params = [])
    {
        if (!$this->_widgetIsAllowed($params)) {
            return '';
        }
        $widget = new Element($id, $params, $this->getDI());

        return $widget->render();
    }

    /**
     * Render widget by identity.
     *
     * @param mixed $id     Widget id in widgets table.
     * @param array $params Widgets params in page.
     *
     * @return mixed
     */
    protected function _renderWidgetId($id, $params = [])
    {
        return $this->_renderWidget((int)$id, $params);
    }

    /**
     * Check that this widget is allowed for current user.
     *
     * @param array $params User params.
     *
     * @return bool
     */
    protected function _widgetIsAllowed($params)
    {
        $viewer = User::getViewer();
        if (empty($params['roles']) || !is_array($params['roles'])) {
            return true;
        }

        return in_array($viewer->role_id, $params['roles']);
    }
}