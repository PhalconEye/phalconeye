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
namespace Engine\Package;

use Engine\Behavior\DIBehavior;
use Engine\Package\PackageGenerator;
use Phalcon\Di;

/**
 * Package data.
 *
 * @category  PhalconEye\Engine
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageData
{
    const
        /**
         * Description of package.
         */
        METADATA_DESCRIPTION = 'description',

        /**
         * Disabled flag.
         */
        METADATA_DISABLED = 'disabled',

        /**
         * Is system.
         */
        METADATA_IS_SYSTEM = 'isSystem',

        /**
         * Events.
         */
        METADATA_EVENTS = 'events';

    private $_name;
    private $_nameUpper;
    private $_type;
    private $_typeUpper;
    private $_namespace;
    private $_module;
    private $_moduleUpper;
    private $_path;
    private $_metadata = [];

    /**
     * PackageData constructor.
     *
     * @param string             $name     Package name.
     * @param string             $type     Package type.
     * @param string|null        $module   Package's module.
     * @param DIBehavior|Di|null $di       Provide DI to collect metadata.
     * @param string|null        $path     Package path.
     * @param array              $metadata Some metadata.
     */
    public function __construct($name, $type, $module, $di = null, $path = null, $metadata = [])
    {
        $this->_name = $name;
        $this->_nameUpper = ucfirst($name);
        $this->_type = $type;
        $this->_typeUpper = ucfirst($type);
        $this->_module = $module;
        $this->_moduleUpper = !empty($module) ? ucfirst($module) : null;
        $this->_path = $path;
        $this->_metadata = $metadata;


        if (PackageManager::PACKAGE_TYPE_MODULE == $type) {
            $this->_namespace = $this->_nameUpper;
        } else {
            $this->_namespace = $this->_typeUpper . PackageManager::SEPARATOR_NS . $this->_nameUpper;

            if (!empty($this->_module)) {
                $this->_namespace = $this->_moduleUpper . PackageManager::SEPARATOR_NS . $this->_namespace;
            }
        }

        if ($di && !$path) {
            $this->_path = $this->_getPackagePath($di);
            $this->_collectMetadata();
        } elseif ($path) {
            $this->_collectMetadata();
        }
    }

    /**
     * Get package name.
     *
     * @return string Name.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get upper name (first latter is capital).
     *
     * @return string Upper name.
     */
    public function getNameUpper(): string
    {
        return $this->_nameUpper;
    }

    /**
     * Get package type name.
     *
     * @return string Type name.
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getTypeUpper(): string
    {
        return $this->_typeUpper;
    }

    /**
     * Get package path.
     *
     * @return string Path.
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Get module name.
     *
     * @return string|null Module name.
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Get module name starting from capital letter.
     *
     * @return string Module name.
     */
    public function getModuleUpper()
    {
        return $this->_moduleUpper;
    }

    /**
     * Get namespace.
     *
     * @return string Namespace.
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Get all or specified metadata.
     *
     * @param string|null $key Provide key to get specific metadata value.
     *
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this->_metadata;
        }

        if (isset($this->_metadata[$key])) {
            return $this->_metadata[$key];
        }

        return null;
    }

    /**
     * Check if some metadata key contains 'true' as value.
     *
     * @param string $metadataKey Metadata blue.
     *
     * @return bool Contains or not value as 'true'.
     */
    public function isMetadata($metadataKey)
    {
        return $this->getMetadata($metadataKey) === true;
    }

    /**
     * Set metadata.
     *
     * @param mixed $metadata Metadata.
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }

    /**
     * Check if package is disabled.
     *
     * @return bool Disabled?
     */
    public function isDisabled()
    {
        return $this->isMetadata(self::METADATA_DISABLED);
    }

    /**
     * Get package path.
     *
     * @param DIBehavior|Di $di Dependency injection.
     *
     * @return string Package path.
     */
    protected function _getPackagePath($di) : string
    {
        $packagePath = $di->getRegistry()->directories->{$this->getType() . 's'};
        if ($this->_module != null) {
            $modules = $di->getModules();
            if ($modules->has($this->_module)) {
                $module = $modules->get($this->_module);
                $packagePath = $module->getPath() . $this->getTypeUpper();
            }
        }
        $packagePath .= $this->getNameUpper() . DS;

        return $packagePath;
    }

    /**
     * Get package metadata if possible.
     *
     *
     * @return void
     */
    protected function _collectMetadata()
    {
        $metadataPath = $this->getPath() . PackageManager::PACKAGE_METADATA_FILENAME;

        if (file_exists($metadataPath)) {
            $this->setMetadata(array_merge($this->_metadata, include $metadataPath));
        }
    }
}