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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  +------------------------------------------------------------------------+
*/

namespace Engine\Migration;

use Engine\Package\PackageData;
use Engine\Package\PackageManager;

/**
 * Migration data.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MigrationData
{
    private $_name;
    private $_module;
    private $_class;
    private $_version;
    private $_path;

    /**
     * MigrationData constructor.
     *
     * @param PackageData $module Module data.
     * @param string      $path   Migration full path.
     */
    public function __construct($module, $path)
    {
        $this->_name = basename($path, ".php");
        $this->_module = $module->getName();
        $this->_class = $module->getNamespace() . PackageManager::SEPARATOR_NS . MigrationManager::MIGRATION_NAME .
            PackageManager::SEPARATOR_NS . $this->_name;
        $this->_version = str_replace(MigrationManager::MIGRATION_NAME . '_', '', $this->_name);
        $this->_path = $path;
    }

    /**
     * Get migration full name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get migration module name.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Get migration full class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Get migration version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get migration full path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
}