<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Engine;

use Phalcon\DI;

/**
 * Dependency container trait.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

trait DependencyInjection
{
    /**
     * Dependency injection container.
     *
     * @var DI
     */
    private $_di;

    /**
     * Create object.
     *
     * @param DI $di Dependency injection container.
     */
    public function __construct($di)
    {
        $this->setDI($di);
        $this->_di = $di;
    }

    /**
     * Set DI.
     *
     * @param DI $di Dependency injection container.
     *
     * @return void
     */
    public function setDI($di)
    {
        $this->_di = $di;
    }

    /**
     * Get DI.
     *
     * @return DI
     */
    public function getDI()
    {
        return $this->_di;
    }
}