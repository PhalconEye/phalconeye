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

use Engine\Form;

/**
 * Form element container interface.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface ElementContainerInterface
{
    /**
     * Add element to container.
     *
     * @param AbstractElement $element Element object.
     *
     * @return mixed
     */
    public function add(AbstractElement $element);

    /**
     * Get element by name.
     *
     * @param string $name Element name.
     *
     * @return AbstractElement
     */
    public function get($name);

    /**
     * Check if element is exists.
     *
     * @param string $name Element name.
     *
     * @return bool
     */
    public function has($name);

    /**
     * Get elements.
     *
     * @return AbstractElement[]
     */
    public function getElements();
}