<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

use Phalcon\DI;

use Engine\Generator\Migrations;

abstract class Installer
{
    /**
     * Dependency injection.
     *
     * @var DI null
     */
    protected $di = null;

    /**
     * Current module name.
     *
     * @var null
     */
    private $_moduleName = null;

    public function __construct($di, $moduleName)
    {
        $this->di = $di;
        $this->_moduleName = $moduleName;
    }

    /**
     * Used to install specific database entities or other specific action
     */
    public abstract function install();

    /**
     * Used before package will be removed from the system
     */
    public abstract function remove();

    /**
     * Used to apply some updates
     *
     * @param $currentVersion
     *
     * @return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public abstract function update($currentVersion);

    /**
     * Run migration scripts from ../modules/ModuleName/Migrations folder.
     *
     * @param string $version Version that must be migrated.
     */
    protected function runMigration($version)
    {
        Migrations::run(array(
            'config' => $this->_di->get('config'),
            'migrationsDir' => ROOT_PATH . '/app/modules/' . ucfirst($this->_moduleName) . '/migrations',
            'toVersion' => $version,
            'force' => false
        ));
    }

}