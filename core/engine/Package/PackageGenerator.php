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
use Engine\Package\Exception\PackageExistsException;
use Engine\Utils\FileUtils;
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
class PackageGenerator
{
    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    const
        /**
         * Packages structure location.
         */
        PACKAGE_STRUCTURE_LOCATION = ROOT_PATH . DS . 'core' . DS . 'engine' . DS . 'Package' . DS . 'Structure' . DS;

    /**
     * Versions of each package.
     *
     * @var array
     */
    protected $_packagesVersions = [];

    /**
     * Create package manager.
     *
     * @param DI $di Dependency injection.
     */
    public function __construct($di = null)
    {
        $this->__DIConstruct($di);
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

        if (PackageManager::PACKAGE_TYPE_THEME == $type) {
            $location = $location . $name;
        } elseif (
            (PackageManager::PACKAGE_TYPE_WIDGET == $type || PackageManager::PACKAGE_TYPE_PLUGIN == $type) &&
            !empty($data['module'])
        ) {
            $module = ucfirst($data['module']);
            $location = $locations[PackageManager::PACKAGE_TYPE_MODULE];
            $location = $location . $module . DS . ucfirst($type) . DS . $nameUpper;
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
            PackageManager::PACKAGE_TYPE_MODULE => $registry->directories->modules,
            PackageManager::PACKAGE_TYPE_PLUGIN => $registry->directories->plugins,
            PackageManager::PACKAGE_TYPE_THEME => $registry->directories->themes,
            PackageManager::PACKAGE_TYPE_WIDGET => $registry->directories->widgets
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
            case PackageManager::PACKAGE_TYPE_MODULE:
                $this->createModule($data);
                break;
            case PackageManager::PACKAGE_TYPE_WIDGET:
            case PackageManager::PACKAGE_TYPE_PLUGIN:
                $this->createWidgetOrPlugin($data);
                break;
            case PackageManager::PACKAGE_TYPE_THEME:
                $this->createTheme($data);
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
     * Create theme package.
     *
     * @param array $data Package data.
     *
     * @throws PackageExistsException If package already exists.
     *
     * @return void
     */
    public function createTheme($data)
    {
        $packageLocation = $this->getPackageLocation($data);

        $themesPath = $this->getDI()->getRegistry()->directories->themes . $data['name'];
        if (is_dir($themesPath)) {
            throw new PackageExistsException("Package with that name already exists!");
        }

        $this->_processData($data);
        $this->_copyStructure($data, $packageLocation);
        $this->_replaceVariables($data, $packageLocation);
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
        $type = $data['type'];
        if (!$isExternal) {
            if (
                PackageManager::PACKAGE_TYPE_WIDGET == $type &&
                $this->getDI()->getWidgets()->has(
                    $data['module'] . PackageManager::SEPARATOR_MODULE . $data['nameUpper']
                )
            ) {
                throw new PackageExistsException("Package with that name already exists!");
            }

            if (
                PackageManager::PACKAGE_TYPE_PLUGIN == $type &&
                $this->getDI()->getPlugins()->has(
                    $data['module'] . PackageManager::SEPARATOR_MODULE . $data['nameUpper']
                )
            ) {
                throw new PackageExistsException("Package with that name already exists!");
            }

            return;
        }

        $existingPackages = $config->packages->{$type}->toArray();
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
    private function _processData(&$data)
    {
        $data['defaultModuleUpper'] = ucfirst(Application::CMS_MODULE_CORE);
        $data['nameUpper'] = ucfirst($data['name']);
        $data['moduleNamespace'] = empty($data['module']) ?
            '' : ucfirst($data['module']) . PackageManager::SEPARATOR_NS;
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
}