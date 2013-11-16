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
*/

namespace Engine\Asset;

use Phalcon\Assets\Collection;
use Phalcon\Assets\Filters\Jsmin,
    Phalcon\Assets\Filters\Cssmin,
    Phalcon\Assets\Manager as AssetManager,
    Phalcon\DI,
    Phalcon\Config;

use Engine\Asset\Css\Less,
    Engine\Package\Utilities as FsUtilities;

use Core\Model\Settings;

/**
 * Assets initializer.
 *
 * @category  PhalconEye
 * @package   Engine\Asset
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Manager extends AssetManager
{
    CONST
        // Assets url.
        ASSETS_PATH = 'assets/',

        // Style file name in url.
        FILENAME_STYLE = 'style.css',

        // Javascript file name in url.
        FILENAME_JAVASCRIPT = 'javascript.js';

    /**
     * Dependency injection.
     *
     * @var DI array|null
     */
    protected $_di;

    /**
     * Application config.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Initialize assets manager.
     *
     * @param DI $di Dependency injection.
     */
    public function __construct($di)
    {
        $this->_di = $di;
        $this->_config = $di->get('config');
        $this->prepare();
    }

    /**
     * Prepare asset manager.
     *
     * @return void
     */
    public function prepare()
    {
        if ($this->_config->application->debug) {
            $this->installAssets();
        }

        $this->set('css', $this->getEmptyCssCollection());
        $this->set('js', $this->getEmptyJsCollection());
    }

    /**
     * Install assets from all modules.
     *
     * @return void
     */
    public function installAssets()
    {
        if (!$this->_config->installed) {
            return;
        }
        $location = $this->_config->application->assets->local;

        ///////////////////////////////////
        // Compile themes css.
        ///////////////////////////////////
        $less = Less::factory();
        $themeDirectory = PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme');
        $themeFiles = glob($themeDirectory . '/*.less');
        FsUtilities::fsCheckLocation($location . 'css/');
        foreach ($themeFiles as $file) {
            $newFileName = $location . 'css/' . basename($file, '.less') . '.css';
            $less->checkedCompile($file, $newFileName);
        }

        ///////////////////////////////////
        // Collect css/js/img from modules.
        ///////////////////////////////////
        foreach ($this->_di->get('modules') as $module => $enabled) {
            if (!$enabled) continue;

            // CSS
            $assetsPath = $this->_config->application->modulesDir . ucfirst($module) . '/Assets/';
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
     * @param bool $refresh Install and compile new assets?
     *
     * @return void
     */
    public function clear($refresh = true)
    {
        $files = FsUtilities::fsRecursiveGlob(PUBLIC_PATH . '/assets/', '*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                @unlink($file); // delete file
        }

        if ($refresh) {
            $this->installAssets();
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
            ->setTargetPath(self::ASSETS_PATH . self::FILENAME_JAVASCRIPT)
            ->setTargetUri(self::ASSETS_PATH . self::FILENAME_JAVASCRIPT)
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
            ->setTargetPath(self::ASSETS_PATH . self::FILENAME_STYLE)
            ->setTargetUri(self::ASSETS_PATH . self::FILENAME_STYLE)
            ->addFilter(new Cssmin())
            ->join(!$this->_config->application->debug);
    }
}