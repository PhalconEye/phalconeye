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
 * @Source("packages")
 * @HasMany("id", '\Core\Model\PackageDependency', "package_id", {
 *  "alias": "PackageDependency"
 * })
 * @HasMany("id", '\Core\Model\PackageDependency', "dependency_id", {
 *  "alias": "RelatedPackages"
 * })
 */
class Package extends \Engine\Model
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
     * Return the related "PackageDependency"
     *
     * @return \Core\Model\PackageDependency[]
     */
    public function getPackageDependency($arguments = array()){
        return $this->getRelated('PackageDependency', $arguments);
    }

    /**
     * Return the related "PackageDependency"
     *
     * @return \Core\Model\PackageDependency[]
     */
    public function getRelatedPackages($arguments = array()){
        return $this->getRelated('RelatedPackages', $arguments);
    }

    /**
     * @param string $type
     * @param null $enabled
     * @param null $order
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public static function findByType($type = \Engine\Package\Manager::PACKAGE_TYPE_MODULE, $enabled = null, $order = null)
    {
        /** @var \Phalcon\Mvc\Model\Query\Builder $query  */
        $query = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
            ->from(array('t' => '\Core\Model\Package'))
            ->where("t.type = '{$type}'");

        if ($enabled !== null){
            $query->andWhere("t.enabled = {$enabled}");
        }

        if ($order !== null){
            $query->orderBy('t.'.$order);
        }

        return $query->getQuery()->execute();
    }

    protected function beforeDelete()
    {
        $this->getPackageDependency()->delete();
    }
}
