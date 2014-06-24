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
    const
        /**
         * Regexp Menu Item pattern.
         */
        ITEM_LINK_PATTERN = "/^((http|https|mailto|ftp):\/\/|javascript:|\/)/";

    use ItemsContainer;

    /** @var string Item label **/
    protected $_label = '';

    /** @var string|null Item link **/
    protected $_link = null;

    /** @var bool Active flag */
    protected $_isActive = false;

    /** @var array Allowed parameters */
    protected $_parameters = [
        'target' => '',
        'onclick' => '',
        'tooltip' => '',
        'tooltip_position' => '',
        'prepend' => '',
        'append' => '',
        'itemPrependContent' => '',
        'itemAppendContent' => ''
    ];

    /**
     * Constructor
     *
     * @param string $label      Link name
     * @param mixed  $link       Optional link target
     * @param array  $parameters Item options
     */
    public function __construct($label, $link = null, $parameters = [])
    {
        // todo: inject di or move this to view
        $di = \Phalcon\DI::getDefault();

        $this->_label =  $di->get('i18n')->query($label);

        if ($link && (is_array($link) || preg_match(static::ITEM_LINK_PATTERN, $link) === 0)) {
            $this->_link = $di->get('url')->get($link);
        } else {
            $this->_link = $link;
        }

        $this->setParameters($parameters);
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
     * Set item (in)active
     *
     * @param bool $active Active flag
     *
     * @return $this
     */
    public function setActive($active = true)
    {
        $this->_isActive = $active;
    }

    /**
     * Get active flag
     *
     * @return string
     */
    public function isActive()
    {
        return $this->_isActive;
    }

    /**
     * Get parameters that will be passed through to View
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Set parameters that will be passed through to View
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->setParameter($name, $value);
        }
        return $this;
    }

    /**
     * Get value of a parameter
     *
     * @param string $name  Parameter name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        if (isset($this->_parameters[$name])) {
            return $this->_parameters[$name];
        }
        return null;
    }

    /**
     * Change a parameter
     *
     * @param string $name  Parameter name
     * @param mixed  $value Parameter value
     *
     * @return $this
     */
    public function setParameter($name, $value)
    {
        if (array_key_exists($name, $this->_parameters)) {
            $this->_parameters[$name] = $value;
        }
    }

    /**
     * Renders Link
     */
    public function buildLinkParameters()
    {
        $params = $this->getParameters();
        $result = [];

        if (!empty($this->_link)) {
            $result['href'] = $this->_link;
        }

        if (!empty($params['onclick'])) {
            $result['onclick'] = $params['onclick'];
        }

        if (!empty($params['target'])) {
            $result['target'] = $params['target'];
        }

        if (!empty($params['tooltip'])) {
            $result['title'] = $params['tooltip'];
            $result['data-tooltip-position'] = $params['tooltip_position'];
        }

        return $result;
    }
}
