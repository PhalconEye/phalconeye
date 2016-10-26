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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Asset;

use Engine\Asset\Css\Less;
use Engine\Behaviour\DIBehaviour;
use Engine\Utils\FileUtils;
use Phalcon\Assets\Filters\Cssmin;
use Phalcon\Assets\Filters\Jsmin;
use Phalcon\Assets\Manager as AssetManager;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Registry;

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
         * Assets path.
         */
        ASSETS_PATH = '/assets/';

    const
        /**
         * Javascript default collection name.
         */
        DEFAULT_COLLECTION_JS = 'js',

        /**
         * CSS default collection name.
         */
        DEFAULT_COLLECTION_CSS = 'css';

    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * Application config.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Inline <head> code.
     *
     * @var array
     */
    protected $_inline = [];

    /**
     * Initialize assets manager.
     *
     * @param DiInterface $di      Dependency injection.
     * @param bool        $prepare Prepare manager (install assets if in debug and create default collections).
     */
    public function __construct($di, $prepare = true)
    {
        $this->__DIConstruct($di);
        $this->_config = $di->getConfig();
        if ($prepare) {
            $this->set(self::DEFAULT_COLLECTION_CSS, $this->getEmptyCssCollection());
            $this->set(self::DEFAULT_COLLECTION_JS, $this->getEmptyJsCollection());
        }
    }

    /**
     * Install assets from all modules.
     *
     * @param string $themeDirectory Theme directory.
     *
     * @return void
     */
    public function installAssets($themeDirectory = '')
    {
        $location = $this->_getLocation();
        $less = Less::factory();
        $less->setVariables(['baseUrl' => "'" . $this->_config->application->baseUrl . "'"]);

        ///////////////////////////////////
        // Compile themes css.
        ///////////////////////////////////
        if ($this->_config->installed && !empty($themeDirectory)) {
            $lessCompileFunction = $this->_config->application->assets->get('lessCompileAlways') ?
                'compileFile' : 'checkedCompile';
            $themeFiles = glob($themeDirectory . '/*.less');
            FileUtils::fsCheckLocation($location . 'css/');
            foreach ($themeFiles as $file) {
                $newFileName = $location . 'css/' . basename($file, '.less') . '.css';
                $less->{$lessCompileFunction}($file, $newFileName);
            }
        }

        ///////////////////////////////////
        // Collect css/js/img from modules and widgets.
        ///////////////////////////////////
        /** @var Registry $registry */
        $registry = $this->getDI()->get('registry');
        $items = array_merge(
            $registry->modules,
            array_fill_keys($registry->widgets, $registry->directories->widgets)
        );
        foreach ($items as $packageName => $sourcePath) {
            // CSS
            $assetsPath = $sourcePath . ucfirst($packageName) . '/Assets/';
            $path = $location . 'css/' . $packageName . '/';
            FileUtils::fsCheckLocation($path);
            $cssFiles = FileUtils::fsRecursiveGlob($assetsPath . 'css/*');
            $less->addImportDir($themeDirectory);
            foreach ($cssFiles as $file) {
                if (!is_file($file)) {
                    continue;
                }
                $fileName = basename($file);
                $fileNameWithoutExt = basename($file, '.less');
                $additionalPath = str_replace($fileName, '', str_replace($assetsPath . 'css/', '', $file));
                if (pathinfo($file, PATHINFO_EXTENSION) == 'less') {
                    FileUtils::fsCheckLocation($path . $additionalPath);
                    $newFileName = $path . $additionalPath . $fileNameWithoutExt . '.css';
                    $less->checkedCompile($file, $newFileName);
                } else {
                    copy($file, $path . $additionalPath . $fileName);
                }
            }

            // JS
            $path = $location . 'js/' . $packageName . '/';
            FileUtils::fsCheckLocation($path);
            FileUtils::fsCopyRecursive($assetsPath . 'js', $path, true);

            // IMAGES
            $path = $location . 'img/' . $packageName . '/';
            FileUtils::fsCheckLocation($path);
            FileUtils::fsCopyRecursive($assetsPath . 'img', $path, true);

            // FONTS
            $path = $location . 'fonts/' . $packageName . '/';
            FileUtils::fsCheckLocation($path);
            FileUtils::fsCopyRecursive($assetsPath . 'fonts', $path, true);
        }
    }

    /**
     * Clear assets cache.
     *
     * @param bool   $refresh        Install and compile new assets?
     * @param string $themeDirectory Theme directory.
     *
     * @return void
     */
    public function clear($refresh = true, $themeDirectory = '')
    {
        $location = $this->_getLocation();
        $files = FileUtils::fsRecursiveGlob($location, '*'); // get all file names
        // iterate files
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file); // delete file
            }
        }

        if ($refresh) {
            $this->installAssets($themeDirectory);
        }
    }

    /**
     * Get empty JS collection.
     *
     * @return Collection
     */
    public function getEmptyJsCollection()
    {
        $collection = new Collection($this->_di);
        $collection
            ->setTargetPath(PUBLIC_PATH . "/assets/compiled.js")
            ->setTargetUri("assets/compiled.js?_=" . time());

        $remote = $this->_config->application->assets->get('remote');
        if ($remote) {
            $collection
                ->setPrefix($remote)
                ->setLocal(false);
        } else {
            $collection->setLocal(true);
        }

        if (!$this->_config->application->debug) {
            $collection
                ->addFilter(new Jsmin())
                ->join(true);
        }

        return $collection;
    }

    /**
     * Get empty CSS collection.
     *
     * @return Collection
     */
    public function getEmptyCssCollection()
    {
        $collection = new Collection($this->_di);
        $collection
            ->setTargetPath(PUBLIC_PATH . "/assets/compiled.css")
            ->setTargetUri("assets/compiled.css?_=" . time());

        $remote = $this->_config->application->assets->get('remote');
        if ($remote) {
            $collection
                ->setPrefix($remote)
                ->setLocal(false);
        } else {
            $collection->setLocal(true);
        }

        if (!$this->_config->application->debug) {
            $collection
                ->addFilter(new Cssmin())
                ->join(true);
        }

        return $collection;
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
     * Get location according to params.
     * Without params - just full path to assets directory.
     *
     * @param null|string $filename Filename append to assets path.
     *
     * @return string
     */
    protected function _getLocation($filename = null)
    {
        $location = PUBLIC_PATH . '/' . $this->_config->application->assets->get('local');
        if (!$filename) {
            return $location;
        }

        return $location . '/' . $filename;
    }
}
