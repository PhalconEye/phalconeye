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

namespace Core;

use Engine\Installer as EngineInstaller;
use Phalcon\Acl as PhalconAcl;

/**
 * Core installer.
 *
 * @category  PhalconEye
 * @package   Core
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
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
        $this->runSqlFile(__DIR__ . '/Assets/sql/installation.sql');
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
        return $currentVersion = null;
    }

}