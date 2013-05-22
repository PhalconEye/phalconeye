<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine\Package;

/**
 * Provides package management
 *
 * Class Manager
 * @package Engine\Package
 */
class Manager
{
    const PACKAGE_TYPE_MODULE = 'module';
    const PACKAGE_TYPE_PLUGIN = 'plugin';
    const PACKAGE_TYPE_THEME = 'theme';
    const PACKAGE_TYPE_WIDGET = 'widget';
    const PACKAGE_TYPE_LIBRARY = 'library';

    /**
     * Allowed types of packages
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

    private $_manifestMinimumData = array(
        'type',
        'name',
        'title',
        'description',
        'version',
    );

    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var \Phalcon\DiInterface
     */
    protected $_di;

    protected $_installedPackages;

    public function __construct($packages = array())
    {
        $this->_di = \Phalcon\DI::getDefault();
        $this->_config = $this->_di->get('config');

        $this->_installedPackages = array();
        if (!empty($packages))
            foreach ($packages as $package) {
                $this->_installedPackages[$package->getType()][$package->getName()] = $package->getVersion();
            }
    }

    /**
     * Get package location in system
     *
     * @param $type
     * @return string
     */
    public function getPackageLocation($type)
    {
        $locations = array(
            self::PACKAGE_TYPE_MODULE => $this->_config->application->modulesDir,
            self::PACKAGE_TYPE_PLUGIN => $this->_config->application->pluginsDir,
            self::PACKAGE_TYPE_THEME => ROOT_PATH . '/public/themes/',
            self::PACKAGE_TYPE_WIDGET => $this->_config->application->widgetsDir,
            self::PACKAGE_TYPE_LIBRARY => $this->_config->application->librariesDir
        );
        if (isset($locations[$type])) {
            return $locations[$type];
        }

        return '';
    }

    /**
     * Create new package according to data
     *
     * @param $data
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
        Utilities::fsCopyRecursive(__DIR__ . '/Structure/' . $data['type'], $packageLocation);

        if ($data['type'] == self::PACKAGE_TYPE_PLUGIN) {
            @rename($packageLocation . '/plugin.php', $packageLocation . '/' . $data['nameUpper'] . '.php');
        }


        // replace placholders in package
        $placeholders = array_keys($data);
        $placeholdersValues = array_values($data);
        foreach ($placeholders as $key => $placeholder) {
            // check header for comment block
            if ($placeholder == 'header' && (strpos($placeholdersValues[$key], '/*') === false || strpos($placeholdersValues[$key], '*/') === false)) {
                $placeholdersValues[$key] = '';
            }

            $placeholders[$key] = '%' . $placeholder . '%';

        }

        foreach (Utilities::fsRecursiveGlob($packageLocation . "/", '*.*') as $filename) {
            $file = file_get_contents($filename);
            file_put_contents($filename, str_replace($placeholders, $placeholdersValues, $file));
        }
    }

    /**
     * Install package using zip archive
     *
     * @param $package zip archive filepath
     * @return \Phalcon\Config
     * @throws Exception
     */
    public function installPackage($package)
    {
        $zip = new \ZipArchive;
        if ($zip->open($package) === true) {
            $zip->extractTo($this->getTempDirectory(false));
            $zip->close();
        } else {
            throw new Exception('Can\'t open archive...');
        }

        $manifest = $this->_readPackageManifest($this->getTempDirectory(false) . 'manifest.php');
        $manifest->offsetSet('isUpdate', false);

        // check itself
        if (isset($this->_installedPackages[$manifest->type][$manifest->name])){
            if ($this->_installedPackages[$manifest->type][$manifest->name] == $manifest->version){
                throw new Exception('This package already installed.');
            }
            else{
                $filter = new \Phalcon\Filter();
                $installedVersion = $filter->sanitize( $this->_installedPackages[$manifest->type][$manifest->name], 'int');
                $packageVersion = $filter->sanitize($manifest->version, 'int');

                if ($installedVersion > $packageVersion){
                    throw new Exception('Newer version of this package already installed.');
                }

                $manifest->offsetSet('isUpdate', true);
                $manifest->offsetSet('currentVersion', $this->_installedPackages[$manifest->type][$manifest->name]);
            }
        }

        // check dependencies
        if ($manifest->get('dependencies')) {
            $filter = new \Phalcon\Filter();
            $missingDependencies = array();
            $wrongVersionDependencies = array();
            $dependencies = $manifest->get('dependencies');
            foreach ($dependencies as $dependecy) {
                if (!isset($this->_installedPackages[$dependecy['type']][$dependecy['name']])) {
                    $missingDependencies[] = $dependecy;
                    continue;
                }

                $installedVersion = $filter->sanitize($this->_installedPackages[$dependecy['type']][$dependecy['name']], 'int');
                $packageDependecyVersion = $filter->sanitize($dependecy['version'], 'int');
                if ($installedVersion < $packageDependecyVersion) {
                    $wrongVersionDependencies[] = $dependecy;
                }
            }

            if (!empty($missingDependencies)) {
                $msg = 'This package requires the presence of the following modules:<br/>';
                foreach ($missingDependencies as $dependecy) {
                    $msg .= sprintf('- %s "%s" (v.%s)<br/>', $dependecy['type'], $dependecy['name'], $dependecy['version']);
                }
                throw new Exception($msg);
            }

            if (!empty($wrongVersionDependencies)) {
                $msg = 'To install this package you need update:<br/>';
                foreach ($wrongVersionDependencies as $dependecy) {
                    $msg .= sprintf('- %s "%s" up to: v.%s. Current version: v.%s <br/>', $dependecy['type'], $dependecy['name'], $dependecy['version'], $this->_installedPackages[$dependecy['type']][$dependecy['name']]);
                }
                throw new Exception($msg);
            }
        }

        // copy files
        if ($manifest->type == self::PACKAGE_TYPE_THEME){
            $destinationDirectory = $this->getPackageLocation($manifest->type).strtolower($manifest->name);
        }
        else{
            $destinationDirectory = $this->getPackageLocation($manifest->type).ucfirst($manifest->name);
        }
        Utilities::fsCopyRecursive($this->getTempDirectory(false) . 'package', $destinationDirectory);

        return $manifest;
    }

    /**
     * Remove package from system
     *
     * @param $name Package name
     * @param $type Package type
     * @throws Exception
     */
    public function removePackage($name, $type)
    {
        $fullName = ucfirst($name);
        $path = $this->getPackageLocation($type) . $fullName;

        if ($type == self::PACKAGE_TYPE_THEME) {
            $path = $this->getPackageLocation($type) . $name;
        }

        if (!is_dir($path)) {
            throw new Exception("Package '{$name}' not found in path '{$path}'.");
        }
        Utilities::fsRmdirRecursive($path, true);

    }

    /**
     * Export package with data
     *
     * @param $name Package name
     * @param $data
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
        $temporaryPackageDir = $temporaryDir . $name . '/';
        $temporaryPackageCopyDir = $temporaryPackageDir . 'package/';


        if (is_dir($temporaryDir))
            Utilities::fsRmdirRecursive($temporaryDir);
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
     * Get temporary directory. This directory is used for unziping package files
     *
     * @param bool $checkDir
     * @return string
     */
    public function getTempDirectory($checkDir = true)
    {
        $directory = ROOT_PATH . '/app/var/temp/packages/';
        if ($checkDir)
            Utilities::fsCheckLocation($directory);
        return $directory;
    }

    /**
     * Clear temporary directory
     */
    public function clearTempDirectory()
    {
        Utilities::fsRmdirRecursive($this->getTempDirectory());
    }

    /**
     * Create manifest file for package
     *
     * @param $filepath
     * @param $data
     */
    private function _createManifest($filepath, $data)
    {
        $manifestData = $this->_manifestDefaultData;
        foreach ($this->_manifestDefaultData as $key => $item) {
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
     * Read package information from manifest file
     *
     * @param $manifestLocation
     * @return \Phalcon\Config
     * @throws Exception\NoManifest
     * @throws Exception\InvalidManifest
     */
    private function _readPackageManifest($manifestLocation)
    {
        // check manifest existense
        if (!file_exists($manifestLocation)) {
            throw new Exception\NoManifest('Missing manifest file in uploaded package.');
        }

        // check manifest is correct
        $manifest = include_once($manifestLocation);
        if (!$manifest || !($manifest instanceof \Phalcon\Config) || !$this->_checkPackageManifest($manifest)) {
            throw new Exception\InvalidManifest('Manifest file is invalid or damaged.');
        }

        return $manifest;
    }

    /**
     * Checks package manifest file
     *
     * @param \Phalcon\Config $manifest
     * @return bool
     */
    private function _checkPackageManifest(\Phalcon\Config $manifest)
    {
        foreach ($this->_manifestMinimumData as $key) {
            if (!array_key_exists($key, $manifest))
                return false;
        }

        return true;
    }

    /**
     * Zip files
     *
     * @param $source
     * @param $destination
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

        $source = str_replace('\\', '/', realpath($source));
        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

}
