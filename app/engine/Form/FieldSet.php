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

namespace Engine\Form;

use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form\Behaviour\FieldSetBehaviour;
use Engine\Form;
use Phalcon\Validation\Message\Group;


/**
 * FieldSet class.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class FieldSet implements ElementContainerInterface
{
    use FieldSetBehaviour {
        FieldSetBehaviour::getElements as protected _getElements;
    }

    /**
     * FieldSet name.
     *
     * @var string
     */
    protected $_name;

    /**
     * Fieldset legend.
     *
     * @var string
     */
    protected $_legend;

    /**
     * Fieldset attributes.
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Combine elements in one container.
     *
     * @var bool
     */
    protected $_combineElements = false;

    /**
     * Check that all elements inside this fieldset must be named (attr 'id') according to it's fieldset.
     *
     * @var bool
     */
    protected $_namedElements = false;

    /**
     * Check that all elements inside this fieldset must be related to this fieldset (name="fieldSet[fieldName]").
     *
     * @var bool
     */
    protected $_dataElements = false;

    /**
     * Create fieldset.
     *
     * @param string $name       FieldSet name.
     * @param string $legend     FieldSet legend.
     * @param array  $attributes FieldSet attributes.
     * @param array  $elements   FieldSet elements.
     */
    public function __construct($name, $legend = null, array $attributes = [], array $elements = [])
    {
        $this->__DIConstruct();

        $this->setName($name);
        $this->setLegend($legend);
        $this->_attributes = array_merge(
            ['id' => $this->getName()],
            $attributes
        );
        $this->_elements = $elements;
        $this->_validation = new Validation($this);

        $this->_errors = new Group();
        $this->_notices = new Group();

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * Get fieldset name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get fieldset legend.
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->_legend;
    }

    /**
     * Set fieldset name.
     *
     * @param string $name Fieldset name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get fieldset legend.
     *
     * @param string $legend Fieldset legend.
     *
     * @return $this
     */
    public function setLegend($legend)
    {
        $this->_legend = $legend;
        return $this;
    }

    /**
     * Check if fieldset has legend.
     *
     * @return string
     */
    public function hasLegend()
    {
        return !empty($this->_legend);
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
     * Combine elements in one container.
     *
     * @param bool $flag Combination flag.
     *
     * @return $this
     */
    public function combineElements($flag = true)
    {
        $this->_combineElements = $flag;
        return $this;
    }

    /**
     * Check if this fieldset must be combined in one container.
     *
     * @return bool
     */
    public function isCombined()
    {
        return $this->_combineElements;
    }

    /**
     * Enable named elements.
     *
     * @param bool $flag Flag.
     *
     * @return $this
     */
    public function enableNamedElements($flag = true)
    {
        $this->_namedElements = $flag;
        return $this;
    }

    /**
     * Enable data elements.
     *
     * @param bool $flag Flag.
     *
     * @return $this
     */
    public function enableDataElements($flag = true)
    {
        $this->_dataElements = $flag;
        return $this;
    }

    /**
     * Render fieldset attributes.
     *
     * @return string
     */
    public function renderAttributes()
    {
        $html = '';
        foreach ($this->_attributes as $key => $attribute) {
            $html .= sprintf(' %s="%s"', $key, $attribute);
        }

        return $html;
    }

    /**
     * Get elements.
     *
     * @return AbstractElement[]
     */
    public function getElements()
    {
        $elements = $this->_getElements();

        if ($this->_namedElements || $this->_dataElements) {
            foreach ($elements as $element) {
                $originalName = $element->getName();
                if ($this->_namedElements) {
                    $element->setName($this->getName() . '_' . $originalName);
                    $element->setAttribute('id', $this->getName() . '_' . $originalName);
                }

                if ($this->_dataElements) {
                    $element->setAttribute('name', $this->getName() . '[' . $originalName . ']');
                }
            }
        }

        return $elements;
    }
}