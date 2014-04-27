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

namespace Engine\Package;

use Engine\Application;
use Engine\Config;
use Engine\Behaviour\DIBehaviour;
use Engine\Package\Exception\InvalidManifest;
use Engine\Package\Model\AbstractPackage;
use Phalcon\DI;
use Phalcon\Filter as PhalconFilter;

/**
 * Package manager.
 *
 * @category  PhalconEye
 * @package   Engine\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
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
        PACKAGE_TYPE_WIDGET = 'widget',

        /**
         * Library package.
         */
        PACKAGE_TYPE_LIBRARY = 'library';

    const
        /**
         * Package manifest file name.
         */
        PACKAGE_MANIFEST_NAME = 'manifest.json';

    /**
     * Allowed types of packages.
     *
     * @var array
     */
    public static $allowedTypes = [
        self::PACKAGE_TYPE_MODULE => 'Module',
        self::PACKAGE_TYPE_PLUGIN => 'Plugin',
        self::PACKAGE_TYPE_THEME => 'Theme',
        self::PACKAGE_TYPE_WIDGET => 'Widget',
        self::PACKAGE_TYPE_LIBRARY => 'Library'
    ];

    /**
     * Minimum required data for correct manifest.
     *
     * @var array
     */
    private $_manifestMinimumData = [
        'type',
        'name',
        'title',
        'description',
        'version',
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
     * @param AbstractPackage|string $package Package.
     *
     * @return string
     */
    public function getPackageLocation($package)
    {
        $registry = $this->getDI()->get('registry');

        // Check additionally module option.
        if ($package instanceof AbstractPackage) {
            $type = $package->type;
            $data = $package->getData();
            if (!empty($data['module'])) {
                $path = $registry->directories->modules . ucfirst($data['module']) . '/Widget/';
                return str_replace('/', DS, $path);
            }
        } else {
            $type = $package;
        }

        $locations = [
            self::PACKAGE_TYPE_MODULE => $registry->directories->modules,
            self::PACKAGE_TYPE_PLUGIN => $registry->directories->plugins,
            self::PACKAGE_TYPE_THEME => PUBLIC_PATH . DS . 'themes' . DS,
            self::PACKAGE_TYPE_WIDGET => $registry->directories->widgets,
            self::PACKAGE_TYPE_LIBRARY => $registry->directories->libraries
        ];
        if (isset($locations[$type])) {
            // fix crossplatform issue directories paths that saved in config.
            return str_replace('/', DS, $locations[$type]);
        }

        return '';
    }

    /**
     * Create new package according to data.
     *
     * @param array $data Package data.
     *
     * @return void
     */
    public function createPackage($data)
    {
        $data['defaultModuleUpper'] = ucfirst(Application::SYSTEM_DEFAULT_MODULE);
        $data['nameUpper'] = ucfirst($data['name']);
        $data['moduleNamespace'] = '';

        if ($data['type'] == self::PACKAGE_TYPE_THEME) {
            $packageLocation = $this->getPackageLocation($data['type']) . $data['name'];
        } elseif ($data['type'] == self::PACKAGE_TYPE_WIDGET && !empty($data['module'])) {
            $data['moduleNamespace'] = ucfirst($data['module']);
            $packageLocation = $this->getPackageLocation(self::PACKAGE_TYPE_MODULE) .
                $data['moduleNamespace'] . '/Widget/' . $data['nameUpper'];
            $data['moduleNamespace'] .= '\\';
        } else {
            $packageLocation = $this->getPackageLocation($data['type']) . $data['nameUpper'];
        }

        Utilities::fsCheckLocation($packageLocation);

        // copy package structure
        Utilities::fsCopyRecursive(
            __DIR__ .
            DIRECTORY_SEPARATOR .
            'Structure' .
            DIRECTORY_SEPARATOR .
            $data['type'],
            $packageLocation,
            false,
            ['.gitignore']
        );

        if ($data['type'] == self::PACKAGE_TYPE_PLUGIN) {
            @rename($packageLocation . DS . 'plugin.php', $packageLocation . DS . $data['nameUpper'] . '.php');
        }

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

        foreach (Utilities::fsRecursiveGlob($packageLocation . DS, '*.*') as $filename) {
            $file = file_get_contents($filename);
            file_put_contents($filename, str_replace($placeholders, $placeholdersValues, $file));
        }
    }

    /**
     * Install package using zip archive.
     *
     * @param string $packageFilePath Zip archive filepath.
     *
     * @throws PackageException
     * @return Config
     */
    public function installPackage($packageFilePath)
    {
        $zip = new \ZipArchive;
        if ($zip->open($packageFilePath) === true) {
            $zip->extractTo($this->getTempDirectory(false));
            $zip->close();
        } else {
            throw new PackageException('Can\'t open archive...');
        }

        $tempDir = rtrim($this->getTempDirectory(false), '/\\');
        $manifestLocation = $tempDir . DS . self::PACKAGE_MANIFEST_NAME;

        // check manifest existence in expected location or its subdir
        if (!file_exists($manifestLocation) && count($tempDirFolders = glob($tempDir . '/*', GLOB_ONLYDIR)) == 1) {
            $tempDir = realpath($tempDirFolders[0]);
            $manifestLocation = $tempDir . DS . self::PACKAGE_MANIFEST_NAME;
        }

        $manifest = $this->_readPackageManifest($manifestLocation);
        $manifest->offsetSet('isUpdate', false);
        $filter = new PhalconFilter();

        // look up for package folder in manifest or fallback to 'package' folder
        if (isset($manifest->source)) {
            $packageDirectory = $tempDir . DS . basename($manifest->source);
        } else {
            $packageDirectory = $tempDir . DS . 'package';
        }

        if (!is_dir($packageDirectory)) {
            throw new PackageException('Missing package folder.');
        }

        // check itself
        if (isset($this->_packagesVersions[$manifest->type][$manifest->name])) {
            if ($this->_packagesVersions[$manifest->type][$manifest->name] == $manifest->version) {
                throw new PackageException('This package already installed.');
            } else {
                $installedVersion = $filter->sanitize(
                    $this->_packagesVersions[$manifest->type][$manifest->name],
                    'int'
                );
                $packageVersion = $filter->sanitize($manifest->version, 'int');

                if ($installedVersion > $packageVersion) {
                    throw new PackageException('Newer version of this package already installed.');
                }

                $manifest->offsetSet('isUpdate', true);
                $manifest->offsetSet('currentVersion', $this->_packagesVersions[$manifest->type][$manifest->name]);
            }
        }

        $this->_checkDependencies($manifest);

        // copy files
        if ($manifest->type == self::PACKAGE_TYPE_THEME) {
            $destinationDirectory = $this->getPackageLocation($manifest->type) . strtolower($manifest->name);
        } elseif ($manifest->type == self::PACKAGE_TYPE_WIDGET && $manifest->offsetExists('module')) {
            $destinationDirectory = $this->getPackageLocation(self::PACKAGE_TYPE_MODULE) . ucfirst($manifest->module) .
                '/Widget/' . ucfirst($manifest->name);
        } else {
            $destinationDirectory = $this->getPackageLocation($manifest->type) . ucfirst($manifest->name);
        }
        Utilities::fsCheckLocation($destinationDirectory);
        Utilities::fsCopyRecursive($packageDirectory, $destinationDirectory);

        return $manifest;
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
        Utilities::fsRmdirRecursive($path, true);
    }

    /**
     * Export package with data.
     *
     * @param AbstractPackage $package Package object.
     * @param array           $params  Additional params.
     *
     * @return void
     */
    public function exportPackage(AbstractPackage $package, array $params = [])
    {
        $location = $this->getPackageLocation($package);
        $packageName = ucfirst($package->name);
        if ($package->type == self::PACKAGE_TYPE_THEME) {
            $location = $location . $package->name;
        } else {
            $location = $location . $packageName;
        }

        $temporaryDir = $this->getTempDirectory();
        $temporaryPackageDir = $temporaryDir . $package->name . DS;
        $temporaryPackageCopyDir = $temporaryPackageDir . 'package' . DS;


        if (is_dir($temporaryDir)) {
            Utilities::fsRmdirRecursive($temporaryDir);
        }
        mkdir($temporaryPackageCopyDir, 0755, true);

        if (is_dir($location)) {
            Utilities::fsCopyRecursive($location, $temporaryPackageCopyDir);
        } else {
            copy($location, $temporaryPackageCopyDir . basename($location));
        }

        $filename = $this->_getPackageFullName($package, true) . '.zip';
        $filepath = $temporaryDir . $filename;

        $this->_createManifest($temporaryPackageDir . self::PACKAGE_MANIFEST_NAME, $package->toJson($params));

        $this->_zip($temporaryPackageDir, $filepath);

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filepath);
    }

    /**
     * Run package installation script.
     *
     * @param Config $manifest Module config.
     *
     * @return string
     */
    public function runInstallScript($manifest)
    {
        if ($manifest->type != self::PACKAGE_TYPE_MODULE) {
            return;
        }

        $installerClass = ucfirst($manifest->name) . '\Installer';
        $newPackageVersion = '0';
        if (file_exists($this->getPackageLocation($manifest->type) . ucfirst($manifest->name) . '/Installer.php')) {
            include_once $this->getPackageLocation($manifest->type) . ucfirst($manifest->name) . '/Installer.php';
        }
        if (class_exists($installerClass)) {
            $packageInstaller = new $installerClass($this->getDI());
            if ($manifest->isUpdate) {
                if (method_exists($packageInstaller, 'update')) {
                    $newVersion = $packageInstaller->update($manifest->currentVersion);
                    $iterations = 0;
                    while ($newVersion !== null && is_string($newVersion) && $iterations < 1000) {
                        $newVersion = $packageInstaller->update($newVersion);
                        if ($newVersion !== null) {
                            $newPackageVersion = $newVersion;
                        }
                        $iterations++;
                    }
                    $package = $this->_getPackage($manifest->type, $manifest->name);
                    $package->version = $newPackageVersion;
                    $package->save();
                }
            } else if (method_exists($packageInstaller, 'install')) {
                $packageInstaller->install();
            }
        }

        return $newPackageVersion;
    }

    /**
     * Get temporary directory. This directory is used for unziping package files.
     *
     * @param bool $checkDir Check directory location.
     *
     * @return string
     */
    public function getTempDirectory($checkDir = true)
    {
        $directory = ROOT_PATH . str_replace('_', DS, '_app_var_temp_packages_');
        if ($checkDir) {
            Utilities::fsCheckLocation($directory);
        }

        return $directory;
    }

    /**
     * Clear temporary directory.
     *
     * @return void
     */
    public function clearTempDirectory()
    {
        Utilities::fsRmdirRecursive($this->getTempDirectory());
    }

    /**
     * Generate packages metadata.
     * Events and modules files.
     *
     * @param AbstractPackage[] $packages      Packages array.
     * @param bool              $checkManifest Check manifest if it can not be just overwritten.
     *
     * @return void
     */
    public function generateMetadata($packages = null, $checkManifest = false)
    {
        if (empty($packages)) {
            $packages = $this->_installedPackages;
        }

        if (empty($packages)) {
            return;
        }

        // Check packages metadata directory.
        $packagesMetadataDirectory = ROOT_PATH . Config::CONFIG_METADATA_PACKAGES;
        Utilities::fsCheckLocation($packagesMetadataDirectory);

        $config = ['installed' => PHALCONEYE_VERSION, 'events' => [], 'modules' => []];
        foreach ($packages as $package) {
            if (!$package->enabled) {
                continue;
            }
            $data = $package->getData();

            if ($package->type == self::PACKAGE_TYPE_MODULE && !$package->is_system) {
                $config['modules'][] = $package->name;
            }

            // Get package events.
            if (
                (in_array($package->type, [self::PACKAGE_TYPE_PLUGIN, self::PACKAGE_TYPE_MODULE])) &&
                !$package->is_system
            ) {
                if (!empty($data) && !empty($data['events'])) {
                    $config['events'] = array_merge($config['events'], $data['events']);
                }
            }

            // If widget is related to module - it has no manifest file.
            if (
                $package->type == self::PACKAGE_TYPE_WIDGET &&
                !empty($data) &&
                !empty($data['module'])
            ) {
                continue;
            }

            $packageMetadataFile = $packagesMetadataDirectory . '/' .
                $this->_getPackageFullName($package) . '.json';
            $this->_createManifest($packageMetadataFile, $package->toJson(), $checkManifest);
        }

        file_put_contents(
            ROOT_PATH . Config::CONFIG_METADATA_APP,
            sprintf('<?php %s return %s;', PHP_EOL, var_export($config, true))
        );
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

    /**
     * Create manifest file for package.
     *
     * @param string $filepath  Manifest path.
     * @param string $content   Manifest content.
     * @param bool   $checkFile Check file existence.
     *
     * @return void
     */
    private function _createManifest($filepath, $content, $checkFile = false)
    {
        if (!$checkFile || !file_exists($filepath)) {
            file_put_contents($filepath, $content);
        }
    }

    /**
     * Read package information from manifest file.
     *
     * @param string $manifestLocation Manifest path.
     *
     * @throws InvalidManifest
     * @return Config
     */
    private function _readPackageManifest($manifestLocation)
    {
        // check manifest existence
        if (!file_exists($manifestLocation)) {
            throw new InvalidManifest('Missing manifest file in uploaded package.');
        }

        // check manifest is correct
        $manifest = file_get_contents($manifestLocation);
        if (
            !($manifest = json_decode($manifest, true)) ||
            !$this->_checkPackageManifest($manifest)
        ) {
            throw new InvalidManifest('Manifest file is invalid or damaged.');
        }

        return new Config($manifest);
    }

    /**
     * Checks package manifest file.
     *
     * @param array $manifest Manifest data.
     *
     * @return bool
     */
    private function _checkPackageManifest($manifest)
    {
        foreach ($this->_manifestMinimumData as $key) {
            if (!array_key_exists($key, $manifest)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Zip files.
     *
     * @param string $source      Source path.
     * @param string $destination Destination path.
     *
     * @return bool
     */
    private function _zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZipArchive::CREATE)) {
            return false;
        }

        $source = str_replace('\\', DS, realpath($source));
        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                $file = str_replace('\\', DS, $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, DS) + 1), ['.', '..'])) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . DS, '', $file));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . DS, '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    /**
     * Check package dependencies.
     *
     * @param Config $manifest Package manifest.
     *
     * @throws PackageException
     * @return void
     */
    private function _checkDependencies($manifest)
    {
        // Check dependencies.
        if (!$manifest->get('dependencies')) {
            return;
        }

        $filter = new PhalconFilter();
        $missingDependencies = [];
        $wrongVersionDependencies = [];
        $dependencies = $manifest->get('dependencies');
        foreach ($dependencies as $dependency) {
            if (!isset($this->_packagesVersions[$dependency['type']][$dependency['name']])) {
                $missingDependencies[] = $dependency;
                continue;
            }

            $installedVersion = $filter->sanitize(
                $this->_packagesVersions[$dependency['type']][$dependency['name']],
                'int'
            );
            $packageDependecyVersion = $filter->sanitize($dependency['version'], 'int');
            if ($installedVersion < $packageDependecyVersion) {
                $wrongVersionDependencies[] = $dependency;
            }
        }

        if (!empty($missingDependencies)) {
            $msg = 'This package requires the presence of the following modules:<br/>';
            foreach ($missingDependencies as $dependency) {
                $msg .= sprintf(
                    '- %s "%s" (v.%s)<br/>',
                    $dependency['type'],
                    $dependency['name'],
                    $dependency['version']
                );
            }
            throw new PackageException($msg);
        }

        if (!empty($wrongVersionDependencies)) {
            $msg = 'To install this package you need update:<br/>';
            foreach ($wrongVersionDependencies as $dependency) {
                $msg .= sprintf(
                    '- %s "%s" up to: v.%s. Current version: v.%s <br/>',
                    $dependency['type'],
                    $dependency['name'],
                    $dependency['version'],
                    $this->_packagesVersions[$dependency['type']][$dependency['name']]
                );
            }
            throw new PackageException($msg);
        }
    }
}