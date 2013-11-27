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

namespace Engine\Package;

use Engine\DependencyInjection;
use Engine\Package\Exception\InvalidManifest;
use Phalcon\Config;
use Phalcon\DI;
use Phalcon\Filter as PhalconFilter;

/**
 * Package manager.
 *
 * @category  PhalconEye
 * @package   Engine\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
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

    /**
     * Allowed types of packages.
     *
     * @var array
     */
    public static $allowedTypes = array(
        self::PACKAGE_TYPE_MODULE => 'Module',
        self::PACKAGE_TYPE_PLUGIN => 'Plugin',
        self::PACKAGE_TYPE_THEME => 'Theme',
        self::PACKAGE_TYPE_WIDGET => 'Widget',
        self::PACKAGE_TYPE_LIBRARY => 'Library'
    );

    /**
     * Default data for manifest.
     *
     * @var array
     */
    private $_manifestDefaultData = array(
        'type' => '',
        'name' => '',
        'title' => '',
        'description' => 'PhalconEye Module',
        'version' => PE_VERSION,
        'author' => 'PhalconEye Team',
        'website' => 'http://phalconeye.com/',
        'dependencies' => array(
            array(
                'name' => 'core',
                'type' => self::PACKAGE_TYPE_MODULE,
                'version' => PE_VERSION,
            ),
        ),
        'events' => array(),
        'widgets' => array()
    );

    /**
     * Minimum required data for correct manifest.
     *
     * @var array
     */
    private $_manifestMinimumData = array(
        'type',
        'name',
        'title',
        'description',
        'version',
    );

    /**
     * Installed packages.
     *
     * @var array
     */
    protected $_installedPackages;

    /**
     * Create package manager.
     *
     * @param array $packages Packages.
     * @param DI    $di       Dependency injection.
     */
    public function __construct($packages = array(), $di = null)
    {
        $this->__DIConstruct($di);
        $this->_installedPackages = array();
        if (!empty($packages)) {
            foreach ($packages as $package) {
                $this->_installedPackages[$package->type][$package->name] = $package->version;
            }
        }
    }

    /**
     * Get package location in system.
     *
     * @param string $type Package type.
     *
     * @return string
     */
    public function getPackageLocation($type)
    {
        $config = $this->getDI()->get('config');

        $locations = array(
            self::PACKAGE_TYPE_MODULE => $config->application->modulesDir,
            self::PACKAGE_TYPE_PLUGIN => $config->application->pluginsDir,
            self::PACKAGE_TYPE_THEME => PUBLIC_PATH . DS . 'themes' . DS,
            self::PACKAGE_TYPE_WIDGET => $config->application->widgetsDir,
            self::PACKAGE_TYPE_LIBRARY => $config->application->librariesDir
        );
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
        $data['nameUpper'] = ucfirst($data['name']);

        if ($data['type'] == self::PACKAGE_TYPE_THEME) {
            $packageLocation = $this->getPackageLocation($data['type']) . $data['name'];
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
            array('.gitignore')
        );

        if ($data['type'] == self::PACKAGE_TYPE_PLUGIN) {
            @rename($packageLocation . DS . 'plugin.php', $packageLocation . DS . $data['nameUpper'] . '.php');
        }


        // replace placholders in package
        $placeholders = array_keys($data);
        $placeholdersValues = array_values($data);
        foreach ($placeholders as $key => $placeholder) {
            // check header for comment block
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

        $manifest = $this->_readPackageManifest($this->getTempDirectory(false) . 'manifest.php');
        $manifest->offsetSet('isUpdate', false);

        // check itself
        if (isset($this->_installedPackages[$manifest->type][$manifest->name])) {
            if ($this->_installedPackages[$manifest->type][$manifest->name] == $manifest->version) {
                throw new PackageException('This package already installed.');
            } else {
                $filter = new PhalconFilter();
                $installedVersion = $filter->sanitize(
                    $this->_installedPackages[$manifest->type][$manifest->name],
                    'int'
                );
                $packageVersion = $filter->sanitize($manifest->version, 'int');

                if ($installedVersion > $packageVersion) {
                    throw new PackageException('Newer version of this package already installed.');
                }

                $manifest->offsetSet('isUpdate', true);
                $manifest->offsetSet('currentVersion', $this->_installedPackages[$manifest->type][$manifest->name]);
            }
        }

        // check dependencies
        if ($manifest->get('dependencies')) {
            $filter = new PhalconFilter();
            $missingDependencies = array();
            $wrongVersionDependencies = array();
            $dependencies = $manifest->get('dependencies');
            foreach ($dependencies as $dependecy) {
                if (!isset($this->_installedPackages[$dependecy['type']][$dependecy['name']])) {
                    $missingDependencies[] = $dependecy;
                    continue;
                }

                $installedVersion = $filter->sanitize(
                    $this->_installedPackages[$dependecy['type']][$dependecy['name']],
                    'int'
                );
                $packageDependecyVersion = $filter->sanitize($dependecy['version'], 'int');
                if ($installedVersion < $packageDependecyVersion) {
                    $wrongVersionDependencies[] = $dependecy;
                }
            }

            if (!empty($missingDependencies)) {
                $msg = 'This package requires the presence of the following modules:<br/>';
                foreach ($missingDependencies as $dependecy) {
                    $msg .= sprintf(
                        '- %s "%s" (v.%s)<br/>',
                        $dependecy['type'],
                        $dependecy['name'],
                        $dependecy['version']
                    );
                }
                throw new PackageException($msg);
            }

            if (!empty($wrongVersionDependencies)) {
                $msg = 'To install this package you need update:<br/>';
                foreach ($wrongVersionDependencies as $dependecy) {
                    $msg .= sprintf(
                        '- %s "%s" up to: v.%s. Current version: v.%s <br/>',
                        $dependecy['type'],
                        $dependecy['name'],
                        $dependecy['version'],
                        $this->_installedPackages[$dependecy['type']][$dependecy['name']]
                    );
                }
                throw new PackageException($msg);
            }
        }

        // copy files
        if ($manifest->type == self::PACKAGE_TYPE_THEME) {
            $destinationDirectory = $this->getPackageLocation($manifest->type) . strtolower($manifest->name);
        } else {
            $destinationDirectory = $this->getPackageLocation($manifest->type) . ucfirst($manifest->name);
        }
        Utilities::fsCopyRecursive($this->getTempDirectory(false) . 'package', $destinationDirectory);

        return $manifest;
    }

    /**
     * Remove package from system.
     *
     * @param string $name Package name.
     * @param string $type Package type.
     *
     * @throws PackageException
     * @return void
     */
    public function removePackage($name, $type)
    {
        $fullName = ucfirst($name);
        $path = $this->getPackageLocation($type) . $fullName;

        if ($type == self::PACKAGE_TYPE_THEME) {
            $path = $this->getPackageLocation($type) . $name;
        }

        if (!is_dir($path)) {
            throw new PackageException("Package '{$name}' not found in path '{$path}'.");
        }
        Utilities::fsRmdirRecursive($path, true);

    }

    /**
     * Export package with data.
     *
     * @param string $name Package name.
     * @param array  $data Package data.
     *
     * @return void
     */
    public function exportPackage($name, $data)
    {
        $location = $this->getPackageLocation($data['type']);
        $packageName = ucfirst($name);
        if ($data['type'] == self::PACKAGE_TYPE_THEME) {
            $location = $location . $name;
        } else {
            $location = $location . $packageName;
        }

        $temporaryDir = $this->getTempDirectory();
        $temporaryPackageDir = $temporaryDir . $name . DS;
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

        $filename = $data['type'] . '-' . $name . '-' . $data['version'] . '.zip';
        $filepath = $temporaryDir . $filename;

        $this->_createManifest($temporaryPackageDir . 'manifest.php', $data);

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
     * Create manifest file for package.
     *
     * @param string $filepath Manifest path.
     * @param array  $data     Manifest data.
     *
     * @return void
     */
    private function _createManifest($filepath, $data)
    {
        $manifestData = $this->_manifestDefaultData;
        foreach (array_keys($this->_manifestDefaultData) as $key) {
            if (empty($data[$key])) {
                unset($manifestData[$key]);
                continue;
            }

            $manifestData[$key] = $data[$key];
        }

        $dataText = var_export($manifestData, true);
        $dataText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $dataText);
        file_put_contents($filepath, "<?php " . PHP_EOL . PHP_EOL . "return new \\Phalcon\\Config(" . $dataText . ");");
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
        // check manifest existense
        if (!file_exists($manifestLocation)) {
            throw new InvalidManifest('Missing manifest file in uploaded package.');
        }

        // check manifest is correct
        $manifest = include_once($manifestLocation);
        if (!$manifest || !($manifest instanceof Config) || !$this->_checkPackageManifest($manifest)) {
            throw new InvalidManifest('Manifest file is invalid or damaged.');
        }

        return $manifest;
    }

    /**
     * Checks package manifest file.
     *
     * @param Config $manifest Manifest data.
     *
     * @return bool
     */
    private function _checkPackageManifest(Config $manifest)
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
                if (in_array(substr($file, strrpos($file, DS) + 1), array('.', '..'))) {
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

}
