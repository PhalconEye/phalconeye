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

use Engine\Navigation\Item;

/**
 * Packages Admin Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AdminPackagesNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems([
            ['Modules', 'admin/packages', [
                'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
            ]],
            null,
            ['Themes', 'admin/packages/themes', [
                'prepend' => '<i class="glyphicon glyphicon-leaf"></i>'
            ]],
            ['Widgets', 'admin/packages/widgets', [
                'prepend' => '<i class="glyphicon glyphicon-tags"></i>'
            ]],
            ['Plugins', 'admin/packages/plugins', [
                'prepend' => '<i class="glyphicon glyphicon-resize-full"></i>'
            ]],
            ['Libraries', 'admin/packages/libraries', [
                'prepend' => '<i class="glyphicon glyphicon-book"></i>'
            ]],
            null,
            ['Upload', 'admin/packages/upload', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]],
            ['Create new', 'admin/packages/create', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]],
        ]);
    }
}
