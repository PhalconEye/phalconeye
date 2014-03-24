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

use Engine\Behaviour\DIBehaviour;
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
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
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
     * @param string $value  Element value.
     * @param bool   $escape Try to escape html in value.
     *
     * @return $this
     */
    public function setValue($value, $escape = true)
    {
        $value = $this->_xssClean($value);
        $escape = ($this->getOption('escape') !== null ? $this->getOption('escape') : $escape);
        if ($escape && (is_string($value) && !empty($value))) {
            $value = htmlentities($value);
        }

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
        $default = ['id' => $this->getName(), 'name' => $this->getName(), 'class' => 'form-control'];
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

    /**
     * Clean string, preventing xss.
     * Thanks to PHP community for this function.
     *
     * @param string $data Data to filter.
     *
     * @return string
     */
    protected function _xssClean($data)
    {
        if (empty($data) || !is_string($data)) {
            return $data;
        }

        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns.
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols.
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]
            *a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]
            *c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]
            *c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );

        // Remove namespaced elements (we do not need them).
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags.
            $old_data = $data;
            $data = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)
                |l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '',
                $data
            );
        } while ($old_data !== $data);

        return $data;
    }
}