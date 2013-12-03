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

namespace Core\Model;

/**
 * Package dependency.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("package_dependencies")
 * @BelongsTo("package_id", "Core\Model\Package", "id")
 * @BelongsTo("dependency_id", "Core\Model\Package", "id", {
 *  "alias": "Dependency"
 * })
 */
class PackageDependency extends \Engine\Db\AbstractModel
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
     * @return \Core\Model\Package
     */
    public function getDependencyPackage($arguments = array())
    {
        return $this->getRelated('Dependency', $arguments);
    }
}
