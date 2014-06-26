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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Navigation;

use Core\Model\Menu;
use Core\Model\MenuItem;
use Engine\Navigation\Item as NavigationItem;

/**
 * Menu Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MenuNavigation extends Core
{
    const
        /**
         * Menu item icon template
         */
        ITEM_ICON_TEMPLATE = '<img class="nav-icon nav-icon-%s" alt="%s" src="%s"/>';

    /** @var Menu Instance **/
    protected $_menu = null;

    /**
     * Set Menu Id and fetch Items
     *
     * @param int $id Menu ID
     *
     * @return $this
     */
    public function setMenuId($id)
    {
        $this->_items = [];

        $this->setId($id);

        if ($id) {

            if ($this->_menu = Menu::findFirst($id)) {

                // Fetch top level items
                if ($items = $this->_menu->getMenuItems(['parent_id IS NULL', 'order' => 'item_order ASC'])) {
                    $this->setItems($this->_composeMenuItems($items));
                }
            }
        }

        return $this;
    }

    /**
     * Compose navigation items.
     *
     * @param MenuItem[] $items List of Menu Items
     *
     * @return NavigationItem[]
     */
    private function _composeMenuItems($items)
    {
        $url = $this->getDI()->get('url');
        $navigationItems = [];

        foreach ($items as $item) {

            /** @var MenuItem $item */
            if (!$item->isAllowed() || !$item->is_enabled) {
                continue;
            }

            $navItem = new NavigationItem($item->title, $item->getHref(), [
                'target' => $item->target,
                'onclick' => $item->getOnclick()
            ]);

            // Fetch Sub Items
            $subItems = $item->getMenuItems(['order' => 'item_order ASC']);

            if ($subItems && $subItems->count() > 0) {
                $navItem->setItems($this->_composeMenuItems($subItems));
            }

            // Set tooltip
            if ($tooltip = $item->getTooltip()) {
                $navItem->setOption('tooltip', $tooltip);
                $navItem->setOption('tooltip_position', $item->tooltip_position);
            }

            // Set icon
            if (!empty($item->icon)) {

                $navItem->setOption(
                    ($item->icon_position == MenuItem::ITEM_ICON_POSITION_LEFT? 'prepend' : 'append'),
                    sprintf(static::ITEM_ICON_TEMPLATE, $item->icon_position, $item->title, $url->get($item->icon))
                );
            }

            $navigationItems[] = $navItem;
        }

        return $navigationItems;
    }
}
