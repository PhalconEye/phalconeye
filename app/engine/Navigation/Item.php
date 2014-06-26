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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Navigation;

/**
 * Navigation Item
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Item implements \IteratorAggregate, \Countable
{
    use ItemsContainer;

    /** @var NavigationInterface|Item|null **/
    protected $_parentContainer = null;

    /** @var string Item label **/
    protected $_label = '';

    /** @var string|null Item link **/
    protected $_link = null;

    /** @var array Default options **/
    protected $_options = [
        'active' => false,
        'tooltip' => '',
        'tooltip_position' => '',
        'prepend' => '',
        'append' => '',
        'itemPrependContent' => '',
        'itemAppendContent' => ''
    ];

    /** @var array Default Item link attributes **/
    protected $_attributes = [
        'target' => '',
        'onclick' => ''
    ];

    /**
     * Constructor
     *
     * @param string $label      Link name
     * @param mixed  $link       Optional link target
     * @param array  $options    Item options
     * @param array  $attributes Item Link attributes
     */
    public function __construct($label = '', $link = null, $options = [], $attributes = [])
    {
        $this->_label = $label;
        $this->_link = $link;

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        $this->setAttributes($attributes);

        return $this;
    }

    /**
     * Get Item link
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * Get Item label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Get Item option
     *
     * @param string $name Option name
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        return null;
    }

    /**
     * Set Item option
     *
     * @param string $name  Option name
     * @param string $value Option value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        if (array_key_exists($name, $this->_options)) {
            $this->_options[$name] = $value;
        }

        return $this;
    }

    /**
     * Set item (in)active
     *
     * @param bool $active Active flag
     *
     * @return $this
     */
    public function setActive($active = true)
    {
        $this->_options['active'] = $active;
    }

    /**
     * Get active flag
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->_options['active'];
    }

    /**
     * Does Item belong to another Item?
     *
     * @return bool
     */
    public function isNested()
    {
        return ($this->_parentContainer instanceof Item);
    }

    /**
     * Get navigation container the Item belongs to
     *
     * @param NavigationInterface|Item|null $container Instance
     *
     * @return $this
     */
    public function setParentContainer($container)
    {
        $this->_parentContainer = $container;

        return $this;
    }

    /**
     * Get navigation container the Item belongs to
     *
     * @return NavigationInterface|Item|null
     */
    public function getParentContainer()
    {
        return $this->_parentContainer;
    }

    /**
     * Get top level NavigationInterface instance
     *
     * @return NavigationInterface|null
     */
    public function getNavigation()
    {
        if ($this->_parentContainer instanceof NavigationInterface) {
            return $this->_parentContainer;
        } elseif ($this->_parentContainer instanceof Item) {
            return $this->_parentContainer->getNavigation();
        }

        return null;
    }

    /**
     * Get Item Link attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Set Item link attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * Get Item link attribute
     *
     * @param string $name Attribute name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        return null;
    }

    /**
     * Set Item link attribute
     *
     * @param string $name  Attribute name
     * @param mixed  $value Attribute value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
    }
}
