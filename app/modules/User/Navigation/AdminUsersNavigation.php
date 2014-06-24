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

namespace User\Navigation;

use Core\Navigation\Core;
use Engine\Navigation\Item;

/**
 * User Admin Navigation
 *
 * @category  PhalconEye
 * @package   User\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AdminUsersNavigation extends Core
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems([
            new Item('Users', 'admin/users', [
                'prepend' => '<i class="glyphicon glyphicon-user"></i>'
            ]),
            new Item('Roles', 'admin/users/roles', [
                'prepend' => '<i class="glyphicon glyphicon-share"></i>'
            ]),
            new Item('|'),
            new Item('Create new user', 'admin/users/create', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]),
            new Item('Create new role', 'admin/users/roles-create', [
                'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
            ]),
        ]);
    }
}
