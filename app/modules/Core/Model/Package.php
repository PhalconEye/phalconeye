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

class Package extends \Phalcon\Mvc\Model
{

    public $id;

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $type;

    /**
     * @var string
     *
     */
    protected $title;

    /**
     * @var string
     *
     */
    protected $description;

    /**
     * @var string
     *
     */
    protected $version;

    /**
     * @var string
     *
     */
    protected $author;

    /**
     * @var string
     *
     */
    protected $website;

    /**
     * @var integer
     *
     */
    protected $enabled = 1;

    /**
     * @var integer
     *
     */
    protected $is_system = 0;

    public function initialize()
    {
        $this->hasMany("id", '\Core\Model\PackageDependency', "package_id", array('alias' => 'PackageDependency'));
        $this->hasMany("id", '\Core\Model\PackageDependency', "dependency_id", array('alias' => 'RelatedPackages'));
    }

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
     * Method to set the value of field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Method to set the value of field type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Method to set the value of field title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Method to set the value of field version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Method to set the value of field author
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Method to set the value of field website
     *
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Method to set the value of field enabled
     *
     * @param bool $flag
     */
    public function setEnabled($flag = true)
    {
        $this->enabled = (int)$flag;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the value of field version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the value of field author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Returns the value of field website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Checks if module is enabled
     *
     * @return string
     */
    public function isEnabled()
    {
        return (bool)$this->enabled;
    }

    /**
     * Checks if module is a part of system
     *
     * @return string
     */
    public function isSystem()
    {
        return (bool)$this->is_system;
    }

    public function getSource()
    {
        return "packages";
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

    public function beforeDelete()
    {
        $this->getPackageDependency()->delete();
    }
}
