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

namespace %nameUpper%;

class Installer extends \Engine\Installer{

    /**
     * Used to install specific database entities or other specific action
     */
    public function install(){

    }

    /**
     * Used before package will be removed from the system
     */
    public function remove(){

    }

    /**
     * Used to apply some updates
     *
     * @param $currentVersion
     * @return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public function update($currentVersion){

        return null;
    }

}