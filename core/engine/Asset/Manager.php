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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Asset;

use Engine\Asset\Css\Less;
use Engine\Behavior\DIBehavior;
use Engine\Package\PackageData;
use Engine\Package\PackageManager;
use Engine\Utils\FileUtils;
use Phalcon\Assets\Filters\Cssmin;
use Phalcon\Assets\Filters\Jsmin;
use Phalcon\Assets\Manager as AssetManager;
use Phalcon\Config;
use Phalcon\DiInterface;

/**
 * Assets manager.
 *
 * @category  PhalconEye
 * @package   Engine\Asset
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager extends AssetManager
{
    const
        /**
         * Assets destination path in public folder.
         */
        ASSETS_PUBLIC_PATH = DS . 'assets' . DS,

        /**
         * Assets original path in packages.
         */
        ASSETS_PACKAGE_PATH = 'Assets' . DS,

        /**
         * Assets original path in packages.
         */
        ASSETS_APPLICATION_PATH = DS . 'application' . DS,

        /**
         * Assets original path in packages.
         */
        ASSETS_COMPILED_PATH = DS . 'compiled' . DS,

        ASSETS_TYPE_CSS = 'css',
        ASSETS_TYPE_JS = 'js';

    const
        /**
         * Javascript default collection name.
         */
        DEFAULT_COLLECTION_JS = self::ASSETS_TYPE_JS,

        /**
         * CSS default collection name.
         */
        DEFAULT_COLLECTION_CSS = self::ASSETS_TYPE_CSS;

    const
        DIRECTORY_CSS = self::ASSETS_TYPE_CSS,
        DIRECTORY_JS = self::ASSETS_TYPE_JS,
        DIRECTORY_IMG = 'img',
        DIRECTORY_FONTS = 'fonts';

    use DIBehavior {
        DIBehavior::__construct as protected __DIConstruct;
    }

    /**
     * Application config.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Current theme name.
     *
     * @var string
     */
    protected $_theme;

    /**
     * Inline <head> code.
     *
     * @var array
     */
    protected $_inline = [];

    /**
     * Currently in compilation mode?
     *
     * @var bool
     */
    protected $_inCompilation = false;

    /**
     * Initialize assets manager.
     *
     * @param DiInterface $di Dependency injection.
     */
    public function __construct($di)
    {
        $this->__DIConstruct($di);
        $this->_config = $di->getConfig();
        $this->set(self::DEFAULT_COLLECTION_CSS, $this->getEmptyCssCollection());
        $this->set(self::DEFAULT_COLLECTION_JS, $this->getEmptyJsCollection());
    }

    /**
     * Install assets from all modules.
     *
     * @return void
     */
    public function installAssets()
    {
        $location = $this->_getLocation();
        $less = Less::factory();
        $less->setVariables(['baseUrl' => "'" . $this->_config->core->baseUrl . "'"]);
        $lessCompileFunction = $this->_config->core->assets->lessCompileAlways ?
            'compileFile' : 'checkedCompile';


        $packages = array_merge(
            $this->getDI()->getWidgets()->getPackages(),
            $this->getDI()->getModules()->getPackages(),
            $this->getDI()->getThemes()->getPackages()
        );

        /** @var PackageData $package */
        foreach ($packages as $package) {
            $assetsPath = $package->getPath();

            if ($package->getType() != PackageManager::PACKAGE_TYPE_THEME) {
                $assetsPath .= self::ASSETS_PACKAGE_PATH;
            }

            if (!is_dir($assetsPath)) {
                continue;
            }

            ///////////////////////////////////
            // Compile and/or copy CSS files.
            ///////////////////////////////////
            $path = $location . self::DIRECTORY_CSS . DS . $package->getType() . DS . $package->getName() . DS;
            FileUtils::createIfMissing($path);
            $cssFiles = FileUtils::globRecursive($assetsPath . self::DIRECTORY_CSS . DS . '*');
            $less->addImportDir($this->getThemeDirectory());
            foreach ($cssFiles as $file) {
                if (!is_file($file)) {
                    continue;
                }
                $fileName = basename($file);
                $fileNameWithoutExt = basename($file, '.' . Less::FILE_EXTENSION);
                $additionalPath = str_replace(
                    $fileName,
                    '',
                    str_replace($assetsPath . self::DIRECTORY_CSS . DS, '', $file)
                );
                if (pathinfo($file, PATHINFO_EXTENSION) == Less::FILE_EXTENSION) {
                    FileUtils::createIfMissing($path . $additionalPath);
                    $newFileName = $path . $additionalPath . $fileNameWithoutExt . '.' . self::DIRECTORY_CSS;
                    $less->{$lessCompileFunction}($file, $newFileName);
                } else {
                    copy($file, $path . $additionalPath . $fileName);
                }
            }

            ///////////////////////////////////
            // Copy other folders.
            ///////////////////////////////////
            $directories = [self::DIRECTORY_JS, self::DIRECTORY_IMG, self::DIRECTORY_FONTS];
            foreach ($directories as $directory) {
                $path = $location . $directory . DS . $package->getType() . DS . $package->getName() . DS;
                FileUtils::createIfMissing($path);
                FileUtils::copyRecursive($assetsPath . $directory, $path, true);
            }
        }

        FileUtils::createIfMissing(PUBLIC_PATH . self::ASSETS_PUBLIC_PATH . 'compiled');
    }

    /**
     * Clear assets cache.
     *
     * @param bool $refresh Install and compile new assets?
     *
     * @return void
     */
    public function clear($refresh = true)
    {
        $locations = [
            $this->_getLocation(),
            $this->_getLocation(null, self::ASSETS_COMPILED_PATH)
        ];

        foreach ($locations as $location) {
            if (!is_dir($location)) {
                continue;
            }

            $it = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    @rmdir($file->getRealPath());
                } else {
                    @unlink($file->getRealPath());
                }
            }
            @rmdir($location);
        }

        if ($refresh) {
            $this->installAssets();
        }
    }

    /**
     * Set current theme.
     *
     * @param string $theme Theme name.
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;
    }

    /**
     * Get current theme name.
     *
     * @return string Current theme.
     */
    public function getTheme()
    {
        return $this->_theme;
    }

    /**
     * Get current theme path.
     *
     * @return string
     */
    public function getThemeDirectory()
    {
        return $this->getDI()->getRegistry()->directories->themes . $this->getTheme();
    }

    /**
     * Get empty JS collection.
     *
     * @return Collection
     */
    public function getEmptyJsCollection()
    {
        return $this->_getCollectionFor(self::ASSETS_TYPE_JS);
    }

    /**
     * Get empty CSS collection.
     *
     * @return Collection
     */
    public function getEmptyCssCollection()
    {
        return $this->_getCollectionFor(self::ASSETS_TYPE_CSS);
    }

    /**
     * Get file name by collection using pattern.
     *
     * @param Collection $collection Asset collection.
     * @param string     $pattern    File name pattern.
     *
     * @return string
     */
    public function getCollectionFileName(Collection $collection, $pattern)
    {
        return sprintf($pattern, crc32(serialize($collection)));
    }

    /**
     * Get empty collection.
     *
     * @return Collection
     */
    protected function _getEmptyCollection()
    {
        $collection = new Collection($this->getDI());
        $remote = $this->_config->core->assets->get('remote');
        if ($remote) {
            $collection
                ->setPrefix($remote)
                ->setLocal(false);
        } else {
            $collection->setLocal(true);
        }

        return $collection;
    }

    /**
     * Get collection for type.
     *
     * @param string $type Asset type.
     *
     * @return Collection
     */
    protected function _getCollectionFor($type)
    {
        $collection = $this->_getEmptyCollection();
        $url = $this->getDI()->getRouter()->getRewriteUri();
        $hash = md5(ASSETS_VERSION . '_' . $this->getTheme() . '_' . $url);

        $collection
            ->setTargetPath(PUBLIC_PATH . self::ASSETS_PUBLIC_PATH . "compiled" . DS . "$hash.$type")
            ->setTargetUri("assets/compiled/$hash.$type?_=" . ASSETS_VERSION);

        if (!$this->_config->application->debug) {
            $collection
                ->addFilter($this->_getDefaultFilterFor($type))
                ->join(true);
        }

        return $collection;
    }

    /**
     * Get default filter for collection of specific type.
     *
     * @param string $type Asset type.
     *
     * @return null|Cssmin|Jsmin
     */
    protected function _getDefaultFilterFor($type)
    {
        switch ($type) {
            case self::ASSETS_TYPE_CSS:
                return new Cssmin();

            case self::ASSETS_TYPE_JS:
                return new Jsmin();
        }

        return null;
    }

    /**
     * Get location according to params.
     * Without params - just full path to assets directory.
     *
     * @param null|string $filename  Filename append to assets path.
     * @param string      $directory Directory in assets.
     *
     * @return string
     */
    protected function _getLocation($filename = null, $directory = self::ASSETS_APPLICATION_PATH)
    {
        $location = PUBLIC_PATH . DS . 'assets' . $directory;

        if (!$filename) {
            return $location;
        }

        return $location . DS . $filename;
    }
}
