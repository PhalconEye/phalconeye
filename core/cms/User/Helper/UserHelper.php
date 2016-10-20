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
  +------------------------------------------------------------------------+
*/

namespace User\Helper;

use Engine\Helper\AbstractHelper;
use User\Model\UserModel as UserModel;

/**
 * Viewer helper.
 *
 * @category  PhalconEye
 * @package   User\Helper
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UserHelper extends AbstractHelper
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