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

namespace Core\Model;

class PackageDependency extends \Phalcon\Mvc\Model
{
    public $id;

    public $package_id;

    public $dependency_id;

    public function getSource()
    {
        return "package_dependencies";
    }

    public function getDependencyPackage(){
        return Package::findFirst($this->dependency_id);
    }


}
