<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Core\Widget\Menu;

class Controller extends \Engine\Widget\Controller
{

    public function indexAction()
    {
        $this->view->title  =  $this->getParam('title');

        $menuId = $this->getParam('menu_id');
        $menu = null;
        if ($menuId)
            $menu = \Core\Model\Menu::findFirst($menuId);
        if (!$menu)
            return $this->setNoRender();


        $menuClass = $this->getParam('class', 'nav');
        if (empty($menuClass))
            $menuClass = 'nav';

        $cacheKey = "menu_{$menuId}.cache";
        $navigation = $this->cacheData->get($cacheKey);

        if ($navigation === null) {

            $items = $this->_composeNavigation($menu->getMenuItems(array('parent_id IS NULL', 'order' => 'item_order ASC')));

            if (empty($items)) {
                return $this->setNoRender();
            }

            $navigation = new \Engine\Navigation();
            $navigation
                ->setListClass($menuClass)
                ->setItems($items)
                ->setActiveItem($this->dispatcher->getActionName());

            $this->cacheData->save($cacheKey, $navigation);
        }


        $this->view->navigation = $navigation;
    }

    private function _composeNavigation($items)
    {
        $navigationItems = array();
        $index = 1;
        foreach ($items as $item) {
            /** @var MenuItem $item */
            if (!$item->isAllowed()) continue;
            $subItems = $item->getMenuItems(array('order' => 'item_order ASC'));
            $navigationItems[$index] = array(
                'title' => $item->title
            );

            if ($subItems->count() > 0) {
                $navigationItems[$index]['items'] = $this->_composeNavigation($subItems);
            } else {
                $navigationItems[$index]['href'] = $item->getHref();
                $navigationItems[$index]['target'] = $item->target;
            }

            $navigationItems[$index]['onclick'] = $item->getOnclick();

            $tooltip = $item->getTooltip();
            if (!empty($tooltip)) {
                $navigationItems[$index]['tooltip'] = $item->getTooltip();
                $navigationItems[$index]['tooltip_position'] = $item->icon_position;
            }


            if (!empty($item->icon)) {
                if ($item->icon_position == 'left') {
                    $navigationItems[$index]['prepend'] = "<img class='nav-icon' alt='{$item->title}' src='{$item->icon}'/>";
                } else {
                    $navigationItems[$index]['append'] = "<img class='nav-icon' alt='{$item->title}' src='{$item->icon}'/>";
                }
            }

            $index++;
        }

        return $navigationItems;
    }

}