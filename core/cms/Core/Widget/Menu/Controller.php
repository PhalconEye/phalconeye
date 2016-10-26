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

namespace Core\Widget\Menu;

use Core\Api\AclApi;
use Core\Navigation\MenuNavigation;
use Engine\Widget\Controller as WidgetController;
use User\Model\RoleModel;
use User\Model\UserModel;

/**
 * Menu widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Menu
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{
    const
        /**
         * Cache prefix.
         */
        CACHE_PREFIX = 'menu_cache_key_',

        /**
         * Default List class name
         */
        DEFAULT_LIST_CLASS = 'nav';

    /**
     * Main action.
     */
    public function indexAction()
    {
        $listClass = $this->getParam('class');
        $this->view->title = $this->getParam('title');
        $this->view->navigation = $navigation = new MenuNavigation;

        $navigation
            ->setMenuId($this->getParam('menu_id'))
            ->setActiveItem($this->dispatcher->getActionName())
            ->setOption('listClass', $listClass ?: static::DEFAULT_LIST_CLASS);

        if (count($navigation) === 0) {
            $this->setNoRender();
        }
    }

    /**
     * Cache this widget?
     *
     * @return bool
     */
    public function isCached()
    {
        return true;
    }

    /**
     * Get widget cache key.
     *
     * @return string|null
     */
    public function getCacheKey()
    {
        $key = self::CACHE_PREFIX;

        $role = UserModel::getViewer()->getRole();
        if ($role) {
            $key .= $role->type;
        } else {
            $key .= RoleModel::getRoleByType(AclApi::DEFAULT_ROLE_GUEST)->type;
        }

        $key .= '_' . $this->getDI()->getSession()->get('language');

        return $key;
    }

    /**
     * Is widget display controlled by ACL?
     *
     * @return bool
     */
    public function isAclControlled()
    {
        return true;
    }
}