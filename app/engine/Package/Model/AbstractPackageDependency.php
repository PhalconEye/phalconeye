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

namespace Engine\Package\Model;

use Engine\Db\AbstractModel;

/**
 * Abstract package dependency.
 *
 * @category  PhalconEye
 * @package   Engine\Package\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractPackageDependency extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="package_id", size="11")
     */
    public $package_id;

    /**
     * @Column(type="integer", nullable=false, column="dependency_id", size="11")
     */
    public $dependency_id;

    /**
     * Get related package.
     *
     * @param array $arguments Arguments.
     *
     * @return AbstractPackage
     */
    public function getDependencyPackage($arguments = [])
    {
        return $this->getRelated('Dependency', $arguments);
    }

    /**
     * Get package.
     *
     * @param array $arguments Arguments.
     *
     * @return AbstractPackage
     */
    public function getPackage($arguments = [])
    {
        return $this->getRelated('Core\Model\Package', $arguments);
    }
}
