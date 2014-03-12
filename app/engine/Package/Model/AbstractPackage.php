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

use Engine\Application;
use Engine\Db\AbstractModel;
use Engine\Package\Manager;
use Phalcon\DI;
use Phalcon\Mvc\Model\Resultset\Simple;

/**
 * Abstract package.
 *
 * @category  PhalconEye
 * @package   Engine\Package\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractPackage extends AbstractModel
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
     * @Column(type="text", nullable=true, column="data")
     */
    public $data;

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
     * Get default metadata structure.
     *
     * @return array
     */
    public final function getDefaultMetadata()
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'version' => $this->version,
            'author' => $this->author,
            'website' => $this->website,
            'dependencies' => [
                [
                    'name' => Application::SYSTEM_DEFAULT_MODULE,
                    'type' => Manager::PACKAGE_TYPE_MODULE,
                    'version' => PHALCONEYE_VERSION,
                ],
            ],
            'events' => [],
            'widgets' => [],
            'i18n' => []
        ];
    }

    /**
     * Return the related "AbstractPackageDependency" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return AbstractPackageDependency[]
     */
    public function getPackageDependency($arguments = [])
    {
        return $this->getRelated('PackageDependency', $arguments);
    }

    /**
     * Return the related "AbstractPackageDependency" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return AbstractPackageDependency[]
     */
    public function getRelatedPackages($arguments = [])
    {
        return $this->getRelated('RelatedPackages', $arguments);
    }

    /**
     * Check if there is some related data.
     *
     * @param string $name Data name.
     *
     * @return bool
     */
    public function hasData($name)
    {
        $data = $this->getData();
        if ($data && isset($data[$name])) {
            return true;
        }

        return false;
    }

    /**
     * Get package data, convert json to array.
     *
     * @param bool $assoc Return as associative array.
     *
     * @return array|null
     */
    public function getData($assoc = true)
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        if (!empty($this->data)) {
            return json_decode($this->data, $assoc);
        }

        return null;
    }

    /**
     * Add additional data to package.
     *
     * @param string $name    Data name.
     * @param mixed  $value   Data value.
     * @param bool   $asArray Add data to array.
     *
     * @return $this
     */
    public function addData($name, $value, $asArray = false)
    {
        if (!is_array($this->data)) {
            $this->data = $this->getData();
        }

        if ($asArray) {
            if (!isset($this->data[$name]) || !is_array($this->data[$name])) {
                $this->data[$name] = [];
            }

            $this->data[$name][] = $value;
        } else {
            $this->data[$name] = $value;
        }

        return $this;
    }

    /**
     * Assign package data.
     *
     * @param array      $data      Package data.
     * @param array|null $columnMap Column map.
     *
     * @return $this
     */
    public function assign($data, $columnMap = null)
    {
        parent::assign($data, $columnMap);

        if (
            $data['type'] == Manager::PACKAGE_TYPE_PLUGIN ||
            $data['type'] == Manager::PACKAGE_TYPE_MODULE
        ) {

            $this->data = [
                'events' => (!empty($data['events']) ? $data['events'] : []),
                'widgets' => (!empty($data['widgets']) ? $data['widgets'] : [])
            ];
        }
        if (!empty($data['module'])) {
            $this->addData('module', $data['module']);
        }

        return $this;
    }

    /**
     * Return package as string, package metadata.
     *
     * @param array $params Some additional params.
     *
     * @return string
     */
    abstract public function toJson(array $params = []);

    /**
     * Create package from json string.
     *
     * @param string $content Content data.
     *
     * @return void
     */
    abstract public function fromJson($content);

    /**
     * Logic before save.
     *
     * @return void
     */
    protected function beforeSave()
    {
        if (empty($this->data)) {
            $this->data = null;
        } elseif (is_array($this->data)) {
            $this->data = json_encode($this->data);
        }
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