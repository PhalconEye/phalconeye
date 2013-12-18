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

namespace Core\Helper;

use Core\Model\Page;
use Engine\HelperInterface;
use Phalcon\DiInterface;
use Phalcon\Tag;

/**
 * Content renderer.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RenderContent extends Tag implements HelperInterface
{
    /**
     * Render content from database layout.
     *
     * @param DiInterface $di   Dependency injection.
     * @param array       $args Helper arguments.
     *
     * @return mixed
     */
    static public function _(DiInterface $di, array $args)
    {
        $content = '';
        $page = Page::findFirst("type = '{$args[0]}'");
        $widgets = $page->getWidgets();
        $widgetRender = new RenderWidget();

        foreach ($widgets as $widget) {
            $content .= $widgetRender->_($di, [$widget->widget_id, $widget->getParams()]);
        }

        return $content;
    }
}