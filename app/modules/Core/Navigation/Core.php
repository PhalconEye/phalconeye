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

use Engine\Behaviour\ViewBehaviour;
use Engine\Navigation\NavigationInterface;
use Engine\Navigation\AbstractNavigation;

/**
 * Core Navigation
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Core extends AbstractNavigation implements NavigationInterface
{
    use ViewBehaviour;

    /** @var array Default parameters **/
    protected $_parameters = [
        'listTag'                     => 'ul',
        'listClass'                   => 'nav',
        'dropDownItemClass'           => 'dropdown',
        'dropDownItemMenuClass'       => 'dropdown-menu',
        'dropDownSubItemMenuClass'    => 'dropdown-submenu',
        'dropDownItemToggleClass'     => 'dropdown-toggle',
        'dropDownItemHeaderClass'     => 'nav-header',
        'dropDownItemDividerClass'    => 'divider',
        'listItemTag'                 => 'li',
        'highlightActiveDropDownItem' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getLayoutView()
    {
        return $this->resolveView('partials/navigation/layout', 'core');
    }

}
