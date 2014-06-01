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

namespace Engine\Asset;

use Engine\Asset\Css\Less;
use Engine\Behaviour\DIBehaviour;
use Engine\Exception;
use Engine\Package\Utilities as FsUtilities;
use Phalcon\Assets\Collection;
use Phalcon\Assets\Filters\Cssmin;
use Phalcon\Assets\Filters\Jsmin;
use Phalcon\Assets\Manager as AssetManager;
use Phalcon\Cache\Backend;
use Phalcon\Config;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Tag;

/**
 * Assets initializer.
 *
 * @category  PhalconEye
 * @package   Engine\Asset
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager extends AssetManager
{
    const
        /**
         * Style file name in url.
         */
        FILENAME_PATTERN_CSS = 'style-%s.css',

        /**
         * Javascript file name in url.
         */
        FILENAME_PATTERN_JS = 'javascript-%s.js';

    const
        /**
         * Javascript default collection name.
         */
        DEFAULT_COLLECTION_JS = 'js',

        /**
         * CSS default collection name.
         */
        DEFAULT_COLLECTION_CSS = 'css';

    const

        /**
         * Generated path for files that will be merged and minified.
         */
        GENERATED_STORAGE_PATH = 'gen/';


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
     * Cache.
     *
     * @var Backend
     */
    protected $_cache;

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
        $this->_cache = $di->getCacheData();
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
        // Check generated directory.
        ///////////////////////////////////
        if (!is_dir($location . self::GENERATED_STORAGE_PATH)) {
            mkdir($location . self::GENERATED_STORAGE_PATH);
        }

        ///////////////////////////////////
        // Compile themes css.
        ///////////////////////////////////
        if ($this->_config->installed && !empty($themeDirectory)) {
            $themeFiles = glob($themeDirectory . '/*.less');
            FsUtilities::fsCheckLocation($location . 'css/');
            foreach ($themeFiles as $file) {
                $newFileName = $location . 'css/' . basename($file, '.less') . '.css';
                $less->checkedCompile($file, $newFileName);
            }
        }

        ///////////////////////////////////
        // Collect css/js/img from modules.
        ///////////////////////////////////
        $registry = $this->getDI()->get('registry');
        foreach ($registry->modules as $module) {
            // CSS
            $assetsPath = $registry->directories->modules . ucfirst($module) . '/Assets/';
            $path = $location . 'css/' . $module . '/';
            FsUtilities::fsCheckLocation($path);
            $cssFiles = FsUtilities::fsRecursiveGlob($assetsPath . 'css/*');
            $less->addImportDir($themeDirectory);
            foreach ($cssFiles as $file) {
                if (!is_file($file)) {
                    continue;
                }
                $fileName = basename($file);
                $fileNameWithoutExt = basename($file, '.less');
                $additionalPath = str_replace($fileName, '', str_replace($assetsPath . 'css/', '', $file));
                if (pathinfo($file, PATHINFO_EXTENSION) == 'less') {
                    FsUtilities::fsCheckLocation($path . $additionalPath);
                    $newFileName = $path . $additionalPath . $fileNameWithoutExt . '.css';
                    $less->checkedCompile($file, $newFileName);
                } else {
                    copy($file, $path . $additionalPath . $fileName);
                }
            }

            // JS
            $path = $location . 'js/' . $module . '/';
            FsUtilities::fsCheckLocation($path);
            FsUtilities::fsCopyRecursive($assetsPath . 'js', $path, true);

            // IMAGES
            $path = $location . 'img/' . $module . '/';
            FsUtilities::fsCheckLocation($path);
            FsUtilities::fsCopyRecursive($assetsPath . 'img', $path, true);
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
        $files = FsUtilities::fsRecursiveGlob($location, '*'); // get all file names
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
        $collection = new Collection();

        $remote = $this->_config->application->assets->get('remote');
        if ($remote) {
            $collection
                ->setPrefix($remote)
                ->setLocal(false);
        }

        return $collection
            ->addFilter(new Jsmin())
            ->join(!$this->_config->application->debug);
    }

    /**
     * Get empty CSS collection.
     *
     * @return Collection
     */
    public function getEmptyCssCollection()
    {
        $collection = new Collection();
        $remote = $this->_config->application->assets->get('remote');
        if ($remote) {
            $collection
                ->setPrefix($remote)
                ->setLocal(false);
        }

        return $collection
            ->addFilter(new Cssmin())
            ->join(!$this->_config->application->debug);
    }

    /**
     * Add <head> inline code.
     *
     * @param string $name Identification.
     * @param string $code Code to add to <head> tag.
     *
     * @return $this
     */
    public function addInline($name, $code)
    {
        $this->_inline[$name] = $code;
        return $this;
    }

    /**
     * Remove inline code.
     *
     * @param string $name Identification.
     *
     * @return $this
     */
    public function removeInline($name)
    {
        unset($this->_inline[$name]);
        return $this;
    }

    /**
     * Get <head> tag inline code.
     *
     * @return string
     */
    public function outputInline()
    {
        return implode('\n', $this->_inline);
    }

    /**
     * Prints the HTML for JS resources.
     *
     * @param string $collectionName the name of the collection
     *
     * @return string
     **/
    public function outputJs($collectionName = self::DEFAULT_COLLECTION_JS)
    {
        $remote = $this->_config->application->assets->get('remote');
        $collection = $this->collection($collectionName);
        if (!$remote && $collection->getJoin()) {

            $local = $this->_config->application->assets->get('local');
            $lifetime = $this->_config->application->assets->get('lifetime', 0);

            $filepath = $local . self::GENERATED_STORAGE_PATH . $filename = $filename =
                    $this->getCollectionFileName($collection, self::FILENAME_PATTERN_JS);
            $collection
                ->setTargetPath($filepath)
                ->setTargetUri($filepath);

            if ($this->_cache->exists($filename)) {
                return Tag::javascriptInclude($collection->getTargetUri());
            }
            $res = parent::outputJs($collectionName);
            $this->_cache->save($filename, true, $lifetime);
            return $res;
        }
        return parent::outputJs($collectionName);

    }

    /**
     * Prints the HTML for CSS resources.
     *
     * @param string $collectionName the name of the collection
     *
     * @return string
     **/
    public function outputCss($collectionName = self::DEFAULT_COLLECTION_CSS)
    {
        $remote = $this->_config->application->assets->get('remote');
        $collection = $this->collection($collectionName);
        if (!$remote && $collection->getJoin()) {

            $local = $this->_config->application->assets->get('local');
            $lifetime = $this->_config->application->assets->get('lifetime', 0);

            $filepath = $local . self::GENERATED_STORAGE_PATH . $filename = $filename =
                    $this->getCollectionFileName($collection, self::FILENAME_PATTERN_CSS);

            $collection
                ->setTargetPath($filepath)
                ->setTargetUri($filepath);

            if ($this->_cache->exists($filename)) {
                return Tag::stylesheetLink($collection->getTargetUri());
            }
            $res = parent::outputCss($collectionName);
            $this->_cache->save($filename, true, $lifetime);
            return $res;
        }
        return parent::outputCss($collectionName);

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