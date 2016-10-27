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

use Engine\Application;
use Engine\Behavior\DIBehavior;
use Engine\Config;
use Engine\Package\Exception\InvalidManifestException;
use Engine\Package\Exception\PackageExistsException;
use Engine\Package\Model\AbstractPackage;
use Engine\Utils\FileUtils;
use Engine\Widget\WidgetCatalog;
use Phalcon\DI;
use Phalcon\Filter as PhalconFilter;

/**
 * Package manager.
 *
 * @category  PhalconEye
 * @package   Engine\Package
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager
{
    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

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
         * Package manifest file name.
         */
        PACKAGE_MANIFEST_NAME = 'manifest.json',

        /**
         * Packages structure location.
         */
        PACKAGE_STRUCTURE_LOCATION = ROOT_PATH . DS . 'core' . DS . 'engine' . DS . 'Package' . DS . 'Structure' . DS;

    /**
     * Allowed types of packages.
     *
     * @var array
     */
    public static $ALLOWED_TYPES = [
        self::PACKAGE_TYPE_MODULE => 'Module',
        self::PACKAGE_TYPE_PLUGIN => 'Plugin',
        self::PACKAGE_TYPE_THEME => 'Theme',
        self::PACKAGE_TYPE_WIDGET => 'Widget'
    ];

    /**
     * Versions of each package.
     *
     * @var array
     */
    protected $_packagesVersions = [];

    /**
     * Installed packages.
     *
     * @var AbstractPackage[]
     */
    protected $_installedPackages = [];

    /**
     * Create package manager.
     *
     * @param AbstractPackage[] $packages Packages.
     * @param DI                $di       Dependency injection.
     */
    public function __construct($packages = [], $di = null)
    {
        $this->__DIConstruct($di);
        $this->_installedPackages = $packages;
        if (!empty($packages)) {
            foreach ($packages as $package) {
                $this->_packagesVersions[$package->type][$package->name] = $package->version;
            }
        }
    }

    /**
     * Get package location in system.
     *
     * @param array $data Package.
     *
     * @return string
     */
    public function getPackageLocation($data)
    {
        $type = $data['type'];
        $locations = $this->getPackageLocations();
        if (!isset($locations[$type])) {
            return '';
        }

        $location = str_replace('/', DS, $locations[$type]);
        $name = $data['name'];
        $nameUpper = ucfirst($name);

        if (self::PACKAGE_TYPE_THEME == $type) {
            $location = $location . $name;
        } elseif (
            (self::PACKAGE_TYPE_WIDGET == $type || self::PACKAGE_TYPE_PLUGIN == $type) &&
            !empty($data['module'])
        ) {
            $module = ucfirst($data['module']);
            $location = $locations[self::PACKAGE_TYPE_MODULE];
            $location = $location . $module . DS . self::$ALLOWED_TYPES[$type] . DS . $nameUpper;
        } else {
            $location = $location . $nameUpper;
        }

        return $location;
    }

    /**
     * Get package locations array.
     *
     * @return array
     */
    public function getPackageLocations()
    {
        $registry = $this->getDI()->getRegistry();
        return [
            self::PACKAGE_TYPE_MODULE => $registry->directories->modules,
            self::PACKAGE_TYPE_PLUGIN => $registry->directories->plugins,
            self::PACKAGE_TYPE_THEME => PUBLIC_PATH . DS . 'themes' . DS,
            self::PACKAGE_TYPE_WIDGET => $registry->directories->widgets
        ];
    }

    /**
     * Create new package according to data.
     *
     * @param array $data Package data.
     *
     * @throws PackageExistsException If package already exists.
     *
     * @return void
     */
    public function createPackage($data)
    {
        switch ($data['type']) {
            case Manager::PACKAGE_TYPE_MODULE:
                $this->createModule($data);
                break;
            case Manager::PACKAGE_TYPE_WIDGET:
            case Manager::PACKAGE_TYPE_PLUGIN:
                $this->createWidgetOrPlugin($data);
                break;
        }
    }

    /**
     * Create module package.
     *
     * @param array $data Package data.
     *
     * @throws PackageExistsException If package already exists.
     *
     * @return void
     */
    public function createModule($data)
    {
        $config = $this->getDI()->getConfig();
        $packageName = $data['name'];
        $packageLocation = $this->getPackageLocation($data);

        $this->_processData($data);
        $this->_validateData($data, $config, false);
        $this->_copyStructure($data, $packageLocation);
        $this->_replaceVariables($data, $packageLocation);
        $this->_addPackageToConfig($packageName, $data, $config);
    }

    /**
     * Create widget package.
     *
     * @param array $data Package data.
     *
     * @throws PackageExistsException If package already exists.
     *
     * @return void
     */
    public function createWidgetOrPlugin($data)
    {
        $config = $this->getDI()->getConfig();
        $packageName = ucfirst($data['name']);
        $packageLocation = $this->getPackageLocation($data);
        $isExternal = empty($data['module']);

        $this->_processData($data);
        $this->_validateData($data, $config, $isExternal);
        $this->_copyStructure($data, $packageLocation);
        $this->_replaceVariables($data, $packageLocation);

        // Update packages config if widget is external (not from module).
        if ($isExternal) {
            $this->_addPackageToConfig($packageName, $data, $config);
        }
    }

    /**
     * Remove package from system.
     *
     * @param AbstractPackage $package Package object.
     *
     * @throws PackageException
     * @return void
     */
    public function removePackage($package)
    {
        $fullName = ucfirst($package->name);
        $packageData = $package->getData();

        if ($package->type == self::PACKAGE_TYPE_THEME) {
            $path = $this->getPackageLocation($package->type) . $package->name;
        } elseif ($package->type == self::PACKAGE_TYPE_WIDGET && !empty($packageData['module'])) {
            $path = $this->getPackageLocation(self::PACKAGE_TYPE_MODULE) .
                ucfirst($packageData['module']) . '/Widget/' . $fullName;
        } else {
            $path = $this->getPackageLocation($package->type) . $fullName;
        }

        // Check package metadata.
        $metadataFile = ROOT_PATH . Config::CONFIG_METADATA_PACKAGES . '/' .
            $this->_getPackageFullName($package) . '.json';
        if (file_exists($metadataFile)) {
            @unlink($metadataFile);
        }

        if ($package->type == self::PACKAGE_TYPE_THEME) {
            $path = $this->getPackageLocation($package->type) . $package->name;
        }

        if (!is_dir($path)) {
            throw new PackageException("Package '{$package->name}' not found in path '{$path}'.");
        }
        FileUtils::removeRecursive($path, true);
    }


    /**
     * Validate data. Check that package doesn't exists.
     *
     * @param array          $data       Package data.
     * @param \Engine\Config $config     Config.
     * @param bool           $isExternal Is external plugin (outside of module).
     *
     * @throws PackageExistsException If package already exists.
     *
     * @return void
     */
    private function _validateData($data, $config, $isExternal)
    {
        if ($isExternal) {
            if ($this->getDI()->getWidgets()->has($data['module'] . WidgetCatalog::KEY_SEPARATOR . $data['nameUpper'])
            ) {
                throw new PackageExistsException("Package with that name already exists!");
            }

            return;
        }

        $existingPackages = $config->packages->{$data['type']}->toArray();
        if (in_array($data['name'], $existingPackages) || in_array($data['nameUpper'], $existingPackages)) {
            throw new PackageExistsException("Package with that name already exists!");
        }
    }

    /**
     * Process data. Add required variables to data.
     *
     * @param array $data Package data.
     *
     * @return void
     */
    private function _processData($data)
    {
        $data['defaultModuleUpper'] = ucfirst(Application::CMS_MODULE_CORE);
        $data['nameUpper'] = ucfirst($data['name']);
        $data['moduleNamespace'] = !isset($data['module']) ? '' : ucfirst($data['module']);
    }

    /**
     * Copy package structure.
     *
     * @param array  $data            Package data.
     * @param string $packageLocation Package location.
     *
     * @return void
     */
    private function _copyStructure($data, $packageLocation)
    {
        // Check path.
        FileUtils::createIfMissing($packageLocation);

        // Copy package structure.
        FileUtils::copyRecursive(
            self::PACKAGE_STRUCTURE_LOCATION . $data['type'],
            $packageLocation,
            false,
            ['.gitignore']
        );
    }

    /**
     * Copy package structure.
     *
     * @param string         $value  Package data.
     * @param array          $data   Package data.
     * @param \Engine\Config $config Package location.
     *
     * @return void
     */
    private function _addPackageToConfig($value, $data, $config)
    {
        $existingPackages = $config->packages->{$data['type']}->toArray();
        $existingPackages[] = $value;
        $config->packages->{$data['type']} = $existingPackages;
        $config->save(Config::CONFIG_SECTION_PACKAGES);
    }

    /**
     * Replace variables in files.
     *
     * @param array  $data            Package data.
     * @param string $packageLocation Package location.
     *
     * @return void
     */
    private function _replaceVariables($data, $packageLocation)
    {
        // Replace placeholders in package.
        $placeholders = array_keys($data);
        $placeholdersValues = array_values($data);
        foreach ($placeholders as $key => $placeholder) {
            // Check header for comment block.
            if (
                $placeholder == 'header' &&
                (
                    strpos($placeholdersValues[$key], DS . '*') === false ||
                    strpos($placeholdersValues[$key], '*/') === false
                )
            ) {
                $placeholdersValues[$key] = '';
            }

            $placeholders[$key] = '%' . $placeholder . '%';

        }

        foreach (FileUtils::globRecursive($packageLocation . DS, '*.*') as $filename) {
            $file = file_get_contents($filename);
            file_put_contents($filename, str_replace($placeholders, $placeholdersValues, $file));
        }
    }

    /**
     * Get package naming.
     *
     * @param AbstractPackage $package       Package object.
     * @param bool            $appendVersion Append version to end of file.
     * @param string          $separator     Separator for words.
     *
     * @return string
     */
    private function _getPackageFullName(AbstractPackage $package, $appendVersion = false, $separator = '-')
    {
        $data = [$package->type, $package->name];
        if ($appendVersion) {
            $data[] = $package->version;
        }

        return implode($separator, $data);
    }
}