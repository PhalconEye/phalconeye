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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Navigation;

/**
 * Item Container Behaviour
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait ItemsContainer
{
    /** @var Item[] Items in navigation **/
    protected $_items = [];

    /**
     * Append Item to current container
     *
     * @param Item $item
     *
     * @return $this
     */
    public function appendItem(Item $item)
    {
        $this->_items[] = $item;

        return $this;
    }

    /**
     * Prepend Item to current container
     *
     * @param Item $item
     *
     * @return $this
     */
    public function prependItem(Item $item)
    {
        array_unshift($this->_items, $item);

        return $this;
    }

    /**
     * Append Multiple Items
     *
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->_items = [];

        foreach ($items as $item) {
            $this->appendItem($item);
        }

        return $this;
    }

    /**
     * Get container items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Implements \IteratorAggregate
     *
     * @return Item[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_items);
    }

    /**
     * Implements \Countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * Locate and set Active Item
     *
     * @param string $activeItem Active Item URI
     */
    public function locateActiveItem($activeItem)
    {
        $activeItem = trim($activeItem, '/');

        foreach($this->_items as $item) {
            if (trim($item->getLink(), '/') == $activeItem) {
                $item->setActive();
                return;
            }
            if (count($item)) {
                $item->locateActiveItem($activeItem);
            }
        }
    }
}
