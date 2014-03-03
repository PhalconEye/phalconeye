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

namespace Engine\Form\Behaviour;

use Engine\Db\AbstractModel;
use Engine\Form\AbstractForm;
use Engine\Form;
use Phalcon\Validation\Message;

/**
 * Form trait.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait FormBehaviour
{
    /**
     * Current action.
     *
     * @var string
     */
    protected $_action;

    /**
     * Form title.
     *
     * @var string
     */
    protected $_title;

    /**
     * From description.
     *
     * @var string
     */
    protected $_description;

    /**
     * Form attributes.
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Entities objects.
     *
     * @var array
     */
    protected $_entities = [];

    /**
     * Form current method.
     *
     * @var string
     */
    protected $_method = AbstractForm::METHOD_POST;

    /**
     * Form current encryption type.
     *
     * @var string
     */
    protected $_enctype = AbstractForm::ENCTYPE_URLENCODED;

    /**
     * Use token?
     *
     * @var bool
     */
    protected $_useToken = false;

    /**
     * Use default values if element is empty (Without setting element default - it is 'null').
     *
     * @var bool
     */
    protected $_useDefaultValue = true;

    /**
     * Elements filters.
     *
     * @var array
     */
    protected $_filters = [];

    /**
     * Set form values.
     *
     * @param array $values Form values.
     *
     * @return $this
     */
    abstract public function setValues($values);

    /**
     * Set form action.
     *
     * @param string|array $action Url or router params.
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * Get form action attribute.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Get form method type.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Get form encoding type.
     *
     * @return string
     */
    public function getEncodingType()
    {
        return $this->_enctype;
    }

    /**
     * Set form title.
     *
     * @param string $title Form title.
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Get form title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_($this->_title);
    }

    /**
     * Set form description.
     *
     * @param string $description Form description.
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Get form description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_($this->_description);
    }

    /**
     * Returns the attributes for the element.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Sets the element attribute.
     *
     * @param string $name  Attribute name.
     * @param string $value Attribute value.
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    /**
     * Returns the form attribute.
     *
     * @param string $name Attribute name.
     *
     * @return string|null
     */
    public function getAttribute($name)
    {
        if (!isset($this->_attributes[$name])) {
            return null;
        }

        return $this->_attributes[$name];
    }

    /**
     * Enable form multipart for files transferring.
     *
     * @return $this
     */
    public function setAsMultipart()
    {
        $this->_enctype = AbstractForm::ENCTYPE_MULTIPART;
        return $this;
    }

    /**
     * Check if token must be used.
     *
     * @return bool
     */
    public function useToken()
    {
        return $this->_useToken;
    }

    /**
     * Add element filter.
     *
     * @param string $name   Element name.
     * @param string $filter Element filter type.
     *
     * @return $this
     */
    public function addFilter($name, $filter)
    {
        if (!isset($this->_filters[$name])) {
            $this->_filters[$name] = [];
        }

        $this->_filters[$name][] = $filter;
        return $this;
    }

    /**
     * Clear filter params for element.
     *
     * @param string $name Element name.
     *
     * @return $this
     */
    public function clearFilter($name)
    {
        unset($this->_filters[$name]);
        return $this;
    }


    /**
     * Add entity.
     *
     * @param AbstractModel $entity Entity object.
     * @param string|null   $name   Entity name.
     *
     * @return $this
     */
    public function addEntity($entity, $name = null)
    {
        if ($entity) {
            $this->_entities[$name] = $entity;
            $this->setValues($entity->toArray());
        }
        return $this;
    }

    /**
     * Remove entity.
     *
     * @param string|null $name Entity name.
     *
     * @return $this
     */
    public function removeEntity($name = null)
    {
        unset($this->_entities[$name]);
        return $this;
    }

    /**
     * Check if form has some entity.
     *
     * @param string $name Entity name.
     *
     * @return bool
     */
    public function hasEntity($name)
    {
        return isset($this->_entities[$name]);
    }

    /**
     * Get entity.
     *
     * @param string|null $name Entity name.
     *
     * @return AbstractModel
     * @throws \Engine\Form\Exception
     */
    public function getEntity($name = null)
    {
        if (!$this->hasEntity($name)) {
            throw new Form\Exception(sprintf('Entity with name "%s" not found in container.', $name));
        }

        return $this->_entities[$name];
    }
}