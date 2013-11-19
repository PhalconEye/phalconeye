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
*/

%header%

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
     *
*@return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public function update($currentVersion){

        return null;
    }

}