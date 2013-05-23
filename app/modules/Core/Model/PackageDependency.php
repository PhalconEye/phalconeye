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

/**
 * @Source("package_dependencies")
 */
class PackageDependency extends \Engine\Model
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="package_id")
     */
    public $package_id;

    /**
     * @Column(type="integer", nullable=false, column="dependency_id")
     */
    public $dependency_id;

    /**
     * Get related package
     *
     * @return \Engine\Model
     */
    public function getDependencyPackage(){
        return Package::findFirst($this->dependency_id);
    }


}
