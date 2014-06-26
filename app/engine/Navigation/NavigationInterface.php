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
 * Navigation Interface
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface  NavigationInterface
{
    /**
     * Get navigation view name.
     *
     * @return string
     */
    public function getLayoutView();

    /**
     * Initialize navigation items specific logic.
     *
     * @return void
     */
    public function initialize();

    /**
     * Set navigation id
     *
     * @param string $id Navigation ID
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get navigation id
     *
     * @return int
     */
    public function getId();

    /**
     * Set active item. It can be name or href.
     *
     * @param string $itemName Active item name.
     *
     * @return $this
     */
    public function setActiveItem($itemName = '');

    /**
     * Get active item
     *
     * @return string
     */
    public function getActiveItem();

    /**
     * Get Navigation Options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set Navigation Options
     *
     * @return $this
     */
    public function setOptions(array $options);

    /**
     * Get value of Navigation option
     *
     * @param string $name  Option name
     *
     * @return mixed
     */
    public function getOption($name);

    /**
     * Set Navigation option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return $this
     */
    public function setOption($name, $value);

    /**
     * Render Navigation.
     *
     * @param string $viewName Name of the view file.
     *
     * @return string
     */
    public function render($viewName = null);

}
