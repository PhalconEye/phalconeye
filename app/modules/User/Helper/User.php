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
  +------------------------------------------------------------------------+
*/

namespace User\Helper;

use Engine\Helper;
use Phalcon\DI;
use Phalcon\Tag;
use User\Model\User as UserModel;

/**
 * Viewer helper.
 *
 * @category  PhalconEye
 * @package   User\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class User extends Helper
{
    /**
     * Get current user (viewer).
     *
     * @return UserModel
     */
    public function current()
    {
        return UserModel::getViewer();
    }

    /**
     * Get some user.
     *
     * @param int $id User identity.
     *
     * @return UserModel
     */
    public function get($id)
    {
        return UserModel::findFirstById($id);
    }

    /**
     * Is current viewer is user.
     *
     * @return bool
     */
    public function isUser()
    {
        return (bool)UserModel::getViewer()->id;
    }
}