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

use Engine\DependencyInjection;
use Engine\Form;
use Phalcon\Forms\Element as PhalconElement;

/**
 * Form element.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractElement implements ElementInterface
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
    }

    /**
     * Element name.
     *
     * @var string
     */
    protected $_name;

    /**
     * Fieldset or Form object.
     *
     * @var Form\Behaviour\ContainerBehaviour|Form|FieldSet
     */
    protected $_container;

    /**
     * Element value.
     *
     * @var mixed
     */
    protected $_value;

    /**
     * Element options.
     *
     * @var array
     */
    protected $_options;

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Element constructor.
     *
     * @param string $name       Element name.
     * @param array  $options    Element options.
     * @param array  $attributes Element attributes.
     */
    public function __construct($name, array $options = [], array $attributes = [])
    {
        $this->__DIConstruct();
        $this->_name = $name;
        $this->_options = array_merge($this->getDefaultOptions(), $options);
        $this->_attributes = array_merge($this->getDefaultAttributes(), $attributes);
    }

    /**
     * Set element relation to container (fieldset or form).
     *
     * @param Form\Behaviour\ContainerBehaviour|Form|FieldSet $container Form object.
     *
     * @return $this
     */
    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Get related object (fieldset or form).
     *
     * @return Form\Behaviour\ContainerBehaviour|Form|FieldSet|null
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function useDefaultLayout()
    {
        return true;
    }

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function isIgnored()
    {
        return $this->getOption('ignore');
    }

    /**
     * Sets the element option.
     *
     * @param string $value Element value.
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Returns the element's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Returns element's options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets the element option.
     *
     * @param string $name  Option name.
     * @param string $value Options value.
     *
     * @throws Exception
     * @return $this
     */
    public function setOption($name, $value)
    {
        $allowedOptions = $this->getAllowedOptions();
        if (!in_array($name, $allowedOptions)) {
            throw new Exception(
                sprintf(
                    'Element "%s" has no option "%s". Allowed options: %s.',
                    get_class($this),
                    $name,
                    (!empty($allowedOptions) ? implode(', ', $allowedOptions) : 'None')
                )
            );
        }

        $this->_options[$name] = $value;
        return $this;
    }

    /**
     * Returns the element's option.
     *
     * @param string $name    Option name.
     * @param mixed  $default Default value.
     *
     * @return mixed|null
     */
    public function getOption($name, $default = null)
    {
        if (!isset($this->_options[$name])) {
            return $default;
        }

        return $this->_options[$name];
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
     * Returns the element's attribute.
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
     * Sets the element's name.
     *
     * @param string $name Element name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Returns the element's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return [
            'label',
            'description',
            'required',
            'emptyAllowed',
            'ignore',
            'htmlTemplate',
            'defaultValue'
        ];
    }

    /**
     * Get element default options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [];
    }

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        $default = ['id' => $this->getName(), 'name' => $this->getName()];
        if ($this->getOption('required')) {
            $default['required'] = 'required';
        }
        return $default;
    }

    /**
     * Get attributes as html.
     *
     * @return string
     */
    protected function _renderAttributes()
    {
        $html = '';
        foreach ($this->_attributes as $key => $attribute) {
            $html .= sprintf(' %s="%s"', $key, $attribute);
        }

        return $html;
    }
}