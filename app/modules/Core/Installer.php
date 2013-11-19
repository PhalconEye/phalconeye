<?php
/*
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Core;

use Core\Api\Acl;
use Core\Model\Access;
use Core\Model\Language;
use Core\Model\Menu;
use Core\Model\MenuItem;
use Core\Model\Package;
use Engine\Installer as EngineInstaller;
use Engine\Package\Manager;
use Phalcon\Acl as PhalconAcl;
use User\Installer as UserInstaller;
use User\Model\Role;

/**
 * Core installer.
 *
 * @category  PhalconEye
 * @package   Core
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Installer extends EngineInstaller
{
    CONST
        /**
         * Current package version.
         */
        CURRENT_VERSION = '0.4.0';

    /**
     * Used to install specific database entities or other specific action.
     *
     * @return void
     */
    public function install()
    {
        /**
         * Install user roles.
         */
        $roleAdmin = new Role();
        $roleAdmin->name = 'Admin';
        $roleAdmin->description = 'Administrator';
        $roleAdmin->is_default = 0;
        $roleAdmin->type = Acl::DEFAULT_ROLE_ADMIN;
        $roleAdmin->undeletable = 1;
        $roleAdmin->save();

        $roleUser = new Role();
        $roleUser->name = 'User';
        $roleUser->description = 'Default user role';
        $roleUser->is_default = 1;
        $roleUser->type = Acl::DEFAULT_ROLE_USER;
        $roleUser->undeletable = 1;
        $roleUser->save();

        $roleGuest = new Role();
        $roleGuest->name = 'Guest';
        $roleGuest->description = 'Guest role';
        $roleGuest->is_default = 0;
        $roleGuest->type = Acl::DEFAULT_ROLE_GUEST;
        $roleGuest->undeletable = 1;
        $roleGuest->save();

        /**
         * Install access.
         */
        foreach (array($roleUser->id, $roleGuest->id) as $roleId) {
            $access = new Access();
            $access->object = Acl::ACL_ADMIN_AREA;
            $access->action = 'access';
            $access->role_id = $roleId;
            $access->value = PhalconAcl::DENY;
            $access->save();
        }

        /**
         * Install languages.
         */
        $language = new Language();
        $language->name = 'English';
        $language->locale = 'en';
        $language->save();

        /**
         * Install Menu and menu items.
         */
        $menu = new Menu();
        $menu->name = 'Default menu';
        $menu->save();

        $menuItem = new MenuItem();
        $menuItem->title = 'Home';
        $menuItem->menu_id = $menu->id;
        $menuItem->url = '/';
        $menuItem->icon = '/files/PE_logo.png';
        $menuItem->icon_position = 'left';
        $menuItem->save();

        $menuItem = new MenuItem();
        $menuItem->title = 'Github';
        $menuItem->menu_id = $menu->id;
        $menuItem->url = 'https://github.com/lantian/PhalconEye';
        $menuItem->target = '_blank';
        $menuItem->tooltip = '<p><b><span style="color:#FF0000;">G</span>it<span style="color:#FF0000;">H</span>ub Page</b></p>\r\n';
        $menuItem->tooltip_position = 'left';
        $menuItem->icon = '/files/github.gif';
        $menuItem->icon_position = 'left';
        $menuItem->save();

        /**
         * Write info about this packages.
         */
        foreach (
            array(
                array('name' => 'core', 'title' => 'Core', 'version' => self::CURRENT_VERSION),
                array('name' => 'user', 'title' => 'Users', 'version' => UserInstaller::CURRENT_VERSION)
            )
            as $packageInfo) {
            $package = new Package();
            $package->name = $packageInfo['name'];
            $package->type = Manager::PACKAGE_TYPE_MODULE;
            $package->title = $packageInfo['title'];
            $package->description = 'PhalconEye ' . $packageInfo['title'];
            $package->version = $packageInfo['version'];
            $package->author = 'PhalconEye Team';
            $package->website = 'http://phalconeye.com/';
            $package->enabled = 1;
            $package->is_system = 1;
            $package->save();
        }
    }

    /**
     * Used before package will be removed from the system.
     *
     * @return void
     */
    public function remove()
    {

    }

    /**
     * Used to apply some updates.
     * Return 'string' (new version) if migration is not finished, 'null' if all updates were applied.
     *
     * @param string $currentVersion Current module version.
     *
     * @return string|null
     */
    public function update($currentVersion)
    {
        return null;
    }

}