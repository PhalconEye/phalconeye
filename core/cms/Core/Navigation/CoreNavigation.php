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

namespace Core\Navigation;

use Engine\Behavior\ViewBehavior;
use Engine\Navigation\Item;
use Engine\Navigation\NavigationInterface;
use Engine\Navigation\AbstractNavigation;

/**
 * Core Navigation
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CoreNavigation extends AbstractNavigation implements NavigationInterface
{
    use ViewBehavior;

    /** @var array Default parameters **/
    protected $_options = [
        'listTag'                     => 'ul',
        'listClass'                   => 'nav',
        'dropDownItemClass'           => 'dropdown',
        'dropDownItemMenuClass'       => 'dropdown-menu',
        'dropDownSubItemMenuClass'    => 'dropdown-submenu',
        'dropDownItemToggleClass'     => 'dropdown-toggle',
        'dropDownItemDataToggle'      => 'toggle',
        'dropDownItemHeaderClass'     => 'nav-header',
        'dropDownItemDividerClass'    => 'divider',
        'listItemTag'                 => 'li',
        'linkClass'                   => 'system-tooltip',
        'highlightActiveDropDownItem' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getLayoutView()
    {
        return $this->resolveView('partials/navigation/layout', 'core');
    }

    /**
     * Build Item attributes
     *
     * @param Item $item Instance
     *
     * @return array
     */
    public function getItemAttributes(Item $item)
    {
        $attributes = [];
        $classes = [];
        $isNested = $item->isNested();

        if (count($item)) {
            $classes[] = $this->getOption($isNested? 'dropDownSubItemMenuClass' : 'dropDownItemClass');
        }

        if ($item->isActive()) {
            if (!$isNested || $this->getOption('highlightActiveDropDownItem')) {
                $classes[] = 'active';
            }
        }

        if ($classes) {
            $attributes['class'] = implode(' ', $classes);
        }

        return $attributes;
    }

    /**
     * Build link attributes
     *
     * @param Item $item Instance
     *
     * @return array
     */
    public function getLinkAttributes(Item $item)
    {
        $link = $item->getLink();
        $tooltip = $item->getOption('tooltip');
        $attributes = $item->getAttributes();
        $classes = [$this->getOption('linkClass')];

        // Item own options
        if (!empty($link)) {
            $attributes['href'] = $link;
        }

        if (!empty($tooltip)) {
            $attributes['title'] = $tooltip;
            $attributes['data-tooltip-position'] = $item->getOption('tooltip_position');
        }

        // Navigation options
        if (count($item)) {
            $classes[] = $this->getOption('dropDownItemToggleClass');
            $attributes['data-toggle'] = $this->getOption('dropDownItemDataToggle');
        }

        if ($item->isNested()) {
            $classes[] = $this->getOption('dropDownItemHeaderClass');
        }

        if ($item->getLabel() == '') {
            $classes[] = $this->getOption('dropDownItemDividerClass');
        }

        if (isset($attributes['class'])) {
            $attributes['class'] .= implode(' ', $classes);
        } else {
            $attributes['class'] = implode(' ', $classes);
        }

        return $attributes;
    }
}
