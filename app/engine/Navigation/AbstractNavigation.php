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

use Engine\Behaviour\DIBehaviour;
use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Abstract Navigation.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractNavigation implements NavigationInterface, \IteratorAggregate, \Countable
{
    const
        /**
         * Regexp Menu Item pattern.
         */
        ITEM_LINK_PATTERN = "/^((http|https|mailto|ftp):\/\/|javascript:|\/)/";

    use ItemsContainer,
        DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /** @var int Navigation id **/
    protected $_id = 0;

    /** @var string Currently active item, it can be name or href. */
    protected $_activeItem = '';

    /** @var array Default options **/
    protected $_options = [];

    /**
     * Navigation constructor.
     *
     * @param DiInterface $di Dependency injection.
     */
    public function __construct($di = null)
    {
        $this->__DIConstruct($di);
        $this->_activeItem = substr($this->getDI()->get('request')->get('_url'), 1);

        $this->initialize();
    }

    /**
     * Get navigation view name.
     *
     * @return string
     */
    abstract public function getLayoutView();

    /**
     * Initialize navigation items specific logic.
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Set navigation id
     *
     * @param string $id Navigation ID
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Get navigation id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set active item. It can be name or href.
     *
     * @param string $itemName Active item name.
     *
     * @return $this
     */
    public function setActiveItem($itemName = '')
    {
        $this->_activeItem = $itemName;

        return $this;
    }

    /**
     * Get active item
     *
     * @return string
     */
    public function getActiveItem()
    {
        return $this->_activeItem;
    }

    /**
     * Get Navigation Options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set Navigation Options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
        return $this;
    }

    /**
     * Get value of Navigation option
     *
     * @param string $name  Option name
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
     * Set Navigation option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->_options)) {
            throw new \UnexpectedValueException("Unrecognized option $name");
        }
        $this->_options[$name] = $value;
    }

    /**
     * Render Navigation.
     *
     * @param string $viewName Name of the view file.
     *
     * @return string
     */
    public function render($viewName = null)
    {
        $di = $this->getDI();

        if (!$viewName) {
            $viewName = $this->getLayoutView();
        }

        // Locate active Item
        $this->locateActiveItem($this->_activeItem);

        /** @var \Engine\View $view */
        $view = $di->get('view');

        ob_start();
        $view->partial($viewName, [
            'id' => $this->getId(),
            'navigation' => $this,
        ]);
        $html = ob_get_clean();

        if ($di->getRequest()->isAjax()) {
            $view->setContent($html);
        }

        return $html;
    }
}
