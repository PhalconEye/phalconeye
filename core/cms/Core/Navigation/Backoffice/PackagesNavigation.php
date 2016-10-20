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

namespace Core\Navigation\Backoffice;

use Core\Navigation\CoreNavigation;

/**
 * Packages Admin Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackagesNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems([
            ['Modules', 'backoffice/packages', [
                'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
            ]],
            null,
            ['Themes', 'backoffice/packages/themes', [
                'prepend' => '<i class="glyphicon glyphicon-leaf"></i>'
            ]],
            ['Widgets', 'backoffice/packages/widgets', [
                'prepend' => '<i class="glyphicon glyphicon-tags"></i>'
            ]],
            ['Plugins', 'backoffice/packages/plugins', [
                'prepend' => '<i class="glyphicon glyphicon-resize-full"></i>'
            ]],
            ['Libraries', 'backoffice/packages/libraries', [
                'prepend' => '<i class="glyphicon glyphicon-book"></i>'
            ]],
            null,
            ['Upload', 'backoffice/packages/upload', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]],
            ['Create new', 'backoffice/packages/create', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]],
        ]);
    }
}
