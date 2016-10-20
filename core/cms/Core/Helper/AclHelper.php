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

namespace Core\Helper;

use Engine\Helper\AbstractHelper;
use Phalcon\Acl as PhalconAcl;
use User\Model\UserModel;

/**
 * ACL helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AclHelper extends AbstractHelper
{
    /**
     * Check if action is allowed.
     *
     * @param mixed  $resource Resource.
     * @param string $action   Action to perform.
     *
     * @return bool
     */
    public function isAllowed($resource, $action)
    {
        $viewer = UserModel::getViewer();

        return $this->getDI()
            ->get('core')
            ->acl()
            ->isAllowed($viewer->getRole()->name, $resource, $action) == PhalconAcl::ALLOW;
    }

    /**
     * Check allowed value.
     *
     * @param mixed  $resource  Resource.
     * @param string $valueName Value name.
     *
     * @return mixed
     */
    public function getAllowed($resource, $valueName)
    {
        $viewer = UserModel::getViewer();

        return $this->getDI()
            ->get('core')
            ->acl()
            ->getAllowedValue($resource, $viewer->getRole(), $valueName);
    }
}