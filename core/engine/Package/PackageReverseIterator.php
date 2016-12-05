<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\Package;

use Engine\Behavior\DIBehavior;
use Engine\Cache\System;
use Engine\Exception as EngineException;
use Engine\Package\Exception\NoSuchPackageException;
use Phalcon\Di;

/**
 * Reverse iterator.
 *
 * @category  PhalconEye\Engine
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageReverseIterator implements \Iterator
{
    /**
     * Packages.
     *
     * @var array Packages.
     */
    protected $_packages = [];

    /**
     * PackageReverseIterator constructor.
     *
     * @param array $packages
     */
    public function __construct(array $packages)
    {
        $this->_packages = $packages;
    }

    /**
     * Get current package.
     *
     * @return PackageData
     */
    public function current()
    {
        return current($this->_packages);
    }

    /**
     * Get next package.
     *
     * @return PackageData
     */
    public function next()
    {
        return prev($this->_packages);
    }

    /**
     * Get package key.
     *
     * @return string
     */
    public function key()
    {
        return key($this->_packages);
    }

    /**
     * Check current key is valid.
     *
     * @return bool
     */
    public function valid()
    {
        $key = $this->key();
        return ($key !== NULL && $key !== FALSE);
    }

    /**
     * Reset iterator state.
     */
    public function rewind()
    {
        end($this->_packages);
    }
}