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

use Engine\Db\AbstractModel;
use Engine\Package\Manager;
use Phalcon\DI;
use Phalcon\Mvc\Model\Resultset\Simple;

/**
 * Package.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("packages")
 * @HasMany("id", '\Core\Model\PackageDependency', "package_id", {
 *  "alias": "PackageDependency"
 * })
 * @HasMany("id", '\Core\Model\PackageDependency', "dependency_id", {
 *  "alias": "RelatedPackages"
 * })
 */
class Package extends AbstractModel
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name", size="64")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="type", size="64")
     */
    public $type;

    /**
     * @Column(type="string", nullable=false, column="title", size="64")
     */
    public $title;

    /**
     * @Column(type="text", nullable=true, column="description")
     */
    public $description;

    /**
     * @Column(type="string", nullable=false, column="version", size="32")
     */
    public $version;

    /**
     * @Column(type="string", nullable=true, column="author", size="255")
     */
    public $author;

    /**
     * @Column(type="string", nullable=true, column="website", size="255")
     */
    public $website;

    /**
     * @Column(type="boolean", nullable=false, column="enabled")
     */
    public $enabled = true;

    /**
     * @Column(type="boolean", nullable=false, column="is_system")
     */
    public $is_system = false;

    /**
     * Return the related "PackageDependency" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return PackageDependency[]
     */
    public function getPackageDependency($arguments = [])
    {
        return $this->getRelated('PackageDependency', $arguments);
    }

    /**
     * Return the related "PackageDependency" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return PackageDependency[]
     */
    public function getRelatedPackages($arguments = [])
    {
        return $this->getRelated('RelatedPackages', $arguments);
    }

    /**
     * Find package by type.
     *
     * @param string      $type    Package type.
     * @param null|bool   $enabled Is enabled.
     * @param null|string $order   Order by field.
     *
     * @return Simple
     */
    public static function findByType($type = Manager::PACKAGE_TYPE_MODULE, $enabled = null, $order = null)
    {
        /** @var \Phalcon\Mvc\Model\Query\Builder $query */
        $query = DI::getDefault()->get('modelsManager')->createBuilder()
            ->from(['t' => '\Core\Model\Package'])
            ->where("t.type = '{$type}'");

        if ($enabled !== null) {
            $query->andWhere("t.enabled = {$enabled}");
        }

        if ($order !== null) {
            $query->orderBy('t.' . $order);
        }

        return $query->getQuery()->execute();
    }

    /**
     * Logic before removal.
     *
     * @return void
     */
    protected function beforeDelete()
    {
        $this->getPackageDependency()->delete();
    }
}
