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
 * Pages Admin Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AdminPagesNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems([
            ['Browse', 'admin/pages', [
                'prepend' => '<i class="glyphicon glyphicon-list"></i>'
            ]],
            null,
            ['Create new', 'admin/languages/create', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]]
        ]);
    }
}
