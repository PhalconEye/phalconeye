<?php
namespace Main;

use Engine\AbstractInstaller;

/**
 * Installer for Main.
 *
 * @category PhalconEye\Module
 * @package  Main
 */
class Installer extends AbstractInstaller
{
    /**
     * Used to install specific database entities or other specific action.
     *
     * @return void
     */
    public function install()
    {

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
     *
     * @param string $currentVersion Current version name.
     *
     * @return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public function update($currentVersion)
    {

        return null;
    }
}