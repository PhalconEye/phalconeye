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

namespace Core\Widget\Menu;

use Core\Api\Acl;
use Core\Model\Menu;
use Core\Model\MenuItem;
use Engine\Navigation;
use Engine\Widget\Controller as WidgetController;
use User\Model\Role;
use User\Model\User;

/**
 * Menu widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Header
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{
    const
        /**
         * Cache prefix.
         */
        CACHE_PREFIX = 'menu_cache_key_';

    /**
     * Main action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $this->view->title = $this->getParam('title');

        $menuId = $this->getParam('menu_id');
        $menu = null;
        if ($menuId) {
            $menu = Menu::findFirst($menuId);
        }
        if (!$menu) {
            return $this->setNoRender();
        }


        $menuClass = $this->getParam('class', 'nav');
        if (empty($menuClass)) {
            $menuClass = 'nav';
        }

        $items = $this->_composeNavigationItems(
            $menu->getMenuItems(['parent_id IS NULL', 'order' => 'item_order ASC'])
        );

        if (empty($items)) {
            return $this->setNoRender();
        }

        $navigation = new Navigation();
        $navigation
            ->setListClass($menuClass)
            ->setItems($items)
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->navigation = $navigation;
    }

    /**
     * Compose navigation items.
     *
     * @param MenuItem[] $items Menu items objects.
     *
     * @return array
     */
    private function _composeNavigationItems($items)
    {
        $navigationItems = [];
        $index = 1;
        foreach ($items as $item) {
            /** @var MenuItem $item */
            if (!$item->isAllowed() || !$item->is_enabled) {
                continue;
            }
            $subItems = $item->getMenuItems(['order' => 'item_order ASC']);
            $navigationItems[$index] = ['title' => $item->title];

            if ($subItems && $subItems->count() > 0) {
                $navigationItems[$index]['items'] = $this->_composeNavigationItems($subItems);
            } else {
                $navigationItems[$index]['href'] = $item->getHref();
                $navigationItems[$index]['target'] = $item->target;
            }

            $navigationItems[$index]['onclick'] = $item->getOnclick();

            $tooltip = $item->getTooltip();
            if (!empty($tooltip)) {
                $navigationItems[$index]['tooltip'] = $item->getTooltip();
                $navigationItems[$index]['tooltip_position'] = $item->tooltip_position;
            }


            if (!empty($item->icon)) {
                $iconTemplate = '<img class="nav-icon nav-icon-%s" alt="%s" src="%s"/>';
                $url = $this->getDI()->getUrl();
                if ($item->icon_position == 'left') {
                    $navigationItems[$index]['prepend'] =
                        sprintf($iconTemplate, 'left', $item->title, $url->get($item->icon));
                } else {
                    $navigationItems[$index]['append'] =
                        sprintf($iconTemplate, 'right', $item->title, $url->get($item->icon));
                }
            }

            $index++;
        }

        return $navigationItems;
    }

    /**
     * Cache this widget?
     *
     * @return bool
     */
    public function isCached()
    {
        return true;
    }

    /**
     * Get widget cache key.
     *
     * @return string|null
     */
    public function getCacheKey()
    {
        $key = self::CACHE_PREFIX;

        $role = User::getViewer()->getRole();
        if ($role) {
            $key .= $role->type;
        } else {
            $key .= Role::getRoleByType(Acl::DEFAULT_ROLE_GUEST)->type;
        }

        $key .= '_' . $this->getDI()->getSession()->get('language');

        return $key;
    }
}