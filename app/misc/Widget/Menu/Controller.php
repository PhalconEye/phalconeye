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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Widget_Menu_Controller extends Widget_Controller
{

    public function indexAction()
    {
        $this->view->setVar('title', $this->getParam('title'));

        $menuId = $this->getParam('menu_id');
        $menu = null;
        if ($menuId)
            $menu = Menu::findFirst($menuId);
        if (!$menu)
            return $this->setNoRender();


        $menuClass = $this->getParam('class', 'nav');
        if (empty($menuClass))
            $menuClass = 'nav';

        $cacheKey = "menu_{$menuId}.cache";
        $navigation = $this->cacheData->get($cacheKey);

        if ($navigation === null) {

            $items = $this->_composeNavigation($menu->getMenuItem(array('parent_id IS NULL', 'order' => 'item_order ASC')));

            if (empty($items)) {
                return $this->setNoRender();
            }

            $navigation = new Navigation();
            $navigation
                ->setListClass($menuClass)
                ->setItems($items)
                ->setActiveItem($this->dispatcher->getActionName());

            $this->cacheData->save($cacheKey, $navigation);
        }


        $this->view->setVar('navigation', $navigation);
    }

    private function _composeNavigation($items)
    {
        $navigationItems = array();
        $index = 1;
        foreach ($items as $item) {
            if (!$item->isAllowed()) continue;
            $subItems = $item->getMenuItem(array('order' => 'item_order ASC'));
            $navigationItems[$index] = array(
                'title' => $item->getTitle()
            );

            if ($subItems->count() > 0) {
                $navigationItems[$index]['items'] = $this->_composeNavigation($subItems);
                $navigationItems[$index]['onclick'] = $item->getOnclick();
                $navigationItems[$index]['tooltip'] = $item->getTooltip();
                $navigationItems[$index]['tooltip_position'] = $item->getTooltipPosition();
            } else {
                $navigationItems[$index]['href'] = $item->getHref();
                $navigationItems[$index]['target'] = $item->getTarget();
                $navigationItems[$index]['onclick'] = $item->getOnclick();
                $navigationItems[$index]['tooltip'] = $item->getTooltip();
                $navigationItems[$index]['tooltip_position'] = $item->getTooltipPosition();
            }
            $index++;
        }

        return $navigationItems;
    }

}