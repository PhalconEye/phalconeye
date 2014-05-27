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

namespace Core\Helper;

use Engine\Helper;
use Phalcon\Acl as PhalconAcl;
use Phalcon\DI;
use Phalcon\Tag;
use User\Model\User;

/**
 * ACL helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Acl extends Helper
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
        $viewer = User::getViewer();

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
        $viewer = User::getViewer();

        return $this->getDI()
            ->get('core')
            ->acl()
            ->getAllowedValue($resource, $viewer->getRole(), $valueName);
    }
}