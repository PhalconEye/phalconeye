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
use Phalcon\Di;

/**
 * Package manager.
 *
 * @category  PhalconEye\Engine
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageManager
{
    const
        /**
         * Module package.
         */
        PACKAGE_TYPE_MODULE = 'module',

        /**
         * Plugin package.
         */
        PACKAGE_TYPE_PLUGIN = 'plugin',

        /**
         * Theme package.
         */
        PACKAGE_TYPE_THEME = 'theme',

        /**
         * Widget package.
         */
        PACKAGE_TYPE_WIDGET = 'widget';

    const
        /**
         * Package metadata filename.
         */
        PACKAGE_METADATA_FILENAME = 'metadata.php',

        /**
         * Separator for package key.
         */
        SEPARATOR_MODULE = '|',

        /**
         * Namespace separator.
         */
        SEPARATOR_NS = '\\';

    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    /**
     * Allowed types of packages.
     *
     * @var array
     */
    public static $ALLOWED_TYPES = [
        self::PACKAGE_TYPE_MODULE,
        self::PACKAGE_TYPE_PLUGIN,
        self::PACKAGE_TYPE_THEME,
        self::PACKAGE_TYPE_WIDGET
    ];

    /**
     * Package type.
     *
     * @var string Package type.
     */
    protected $_type;

    /**
     * Packages.
     *
     * @var array Packages.
     */
    protected $_packages = [];

    /**
     * PackageManager constructor.
     *
     * @param DIBehavior|Di $di   Dependency injection.
     * @param string        $type Package type.
     */
    public function __construct($di, $type)
    {
        $this->__DIConstruct($di);
        $this->_type = $type;
    }


    /**
     * Load information about packages.
     */
    public function load()
    {
        $cache = $this->getDI()->getCacheData();
        $cacheKey = sprintf(System::CACHE_KEY_PACKAGES, $this->getType());
        $cachedPackages = $cache->get($cacheKey);
        if (!empty($cachedPackages)) {
            return;
        }

        $packages = $this->getDI()->getConfig()->packages;

        // Add external packages.
        foreach ($packages->{$this->getType()} as $package) {
            $this->add(new PackageData($package, $this->getType(), null, $this->getDI()));
        }

        // Iterate through modules and get packages.
        if (self::PACKAGE_TYPE_MODULE != $this->getType()) {
            $modules = $this->getDI()->getModules()->getPackages();
            /** @var PackageData $module */
            foreach ($modules as $module) {
                $this->addAll($this->_loadPackagesFromModule($module));
            }
        }

        $cache->save($cacheKey, $this->_packages);
    }

    /**
     * Add one package to catalog.
     *
     * @param PackageData $package Package model.
     *
     * @return void
     * @throws EngineException
     */
    public function add(PackageData $package)
    {
        $key = $this->_getKey($package->getName(), $package->getModule());
        if (isset($this->_packages[$key])) {
            throw new EngineException(sprintf('Package catalog has already package with id "%s".', $key));
        }

        $this->_packages[$key] = $package;
    }

    /**
     * Add all packages to catalog.
     *
     * @param array $packages Packages.
     *
     * @return void
     * @throws EngineException
     */
    public function addAll($packages)
    {
        foreach ($packages as $package) {
            $this->add($package);
        }
    }

    /**
     * Get package from catalog.
     *
     * @param string $key Package key.
     *
     * @return PackageData
     * @throws EngineException
     */
    public function get($key) : PackageData
    {
        if (!isset($this->_packages[$key])) {
            throw new EngineException(sprintf('Package catalog has no package with id "%s".', $key));
        }

        return $this->_packages[$key];
    }

    /**
     * Get loaded packages.
     *
     * @return PackageData[] Loaded packages.
     */
    public function getPackages()
    {
        return $this->_packages;
    }

    /**
     * Get handled type.
     *
     * @return string Package type name that current manager is handling.
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Check if package present in packages catalog.
     *
     * @param string $key Package key.
     *
     * @return bool Check result.
     */
    public function has($key) : bool
    {
        return isset($this->_packages[$key]);
    }

    /**
     * Get unique package identifier.
     *
     * @param string      $name   Package name.
     * @param string|null $module Package's module.
     *
     * @return string
     */
    protected function _getKey($name, $module = null) : string
    {
        if (empty($module)) {
            $module = '';
        } else {
            $module .= self::SEPARATOR_MODULE;
        }

        return $module . $name;
    }

    /**
     * Lookup packages in module.
     *
     * @param PackageData $module Module date.
     *
     * @return array List of packages.
     */
    protected function _loadPackagesFromModule($module) : array
    {
        $packages = [];
        $packagePath = $module->getPath() . ucfirst($this->getType());

        if (!is_dir($packagePath)) {
            return [];
        }

        foreach (new \DirectoryIterator($packagePath) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $baseName = $file->getBasename();
                $packages[] = new PackageData(
                    $baseName,
                    $this->getType(),
                    $module->getName(),
                    null,
                    $file->getPath() . DS . $baseName . DS
                );
            }
        }

        return $packages;
    }
}