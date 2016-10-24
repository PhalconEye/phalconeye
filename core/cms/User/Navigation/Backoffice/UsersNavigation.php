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

namespace User\Navigation\Backoffice;

use Core\Navigation\CoreNavigation;

/**
 * User Admin Navigation
 *
 * @category  PhalconEye
 * @package   User\Navigation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UsersNavigation extends CoreNavigation
{
    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setItems(
            [
                ['Users', 'backoffice/users', [
                    'prepend' => '<i class="glyphicon glyphicon-user"></i>'
                ]],
                ['Roles', 'backoffice/users/roles', [
                    'prepend' => '<i class="glyphicon glyphicon-share"></i>'
                ]],
                null,
                ['Create new user', 'backoffice/users/create', [
                    'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                ]],
                ['Create new role', 'backoffice/users/roles-create', [
                    'prepend' => '<i class="glyphicon glyphicon-plus-sign"></i>'
                ]],
            ]
        );
    }
}
