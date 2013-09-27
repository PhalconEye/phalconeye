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

use Phalcon\Assets\Filters\Jsmin,
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
        } else {
            $remote = $this->_config->application->assets->get('remote');
            if ($remote) {
                $this
                    ->collection('css')
                    ->setPrefix($remote)
                    ->setLocal(false);
                $this
                    ->collection('js')
                    ->setPrefix($remote)
                    ->setLocal(false);
            }
            $this
                ->collection('css')
                ->addCss(self::ASSETS_PATH . self::FILENAME_STYLE);

            $this
                ->collection('js')
                ->join(true)
                ->addJs(self::ASSETS_PATH . self::FILENAME_JAVASCRIPT);
        }
    }

    /**
     * Install assets from all modules.
     *
     * @return void
     */
    public function installAssets()
    {
        if ($this->_config && !$this->_config->installed) {
            return;
        }

        $location = $this->_config->application->assets->local;

        ///////////////////////////////////
        // Compile themes css.
        ///////////////////////////////////
        $less = Less::factory();
        $collectedCss = array();
        $themeDirectory = PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme');
        $themeFiles = glob($themeDirectory . '/*.less');
        FsUtilities::fsCheckLocation($location . 'css/');
        foreach ($themeFiles as $file) {
            $newFileName = $location . 'css/' . basename($file, '.less') . '.css';
            $collectedCss[] = $newFileName;
            $less->checkedCompile($file, $newFileName);
        }

        ///////////////////////////////////
        // Collect js/img from modules.
        ///////////////////////////////////
        foreach ($this->_di->get('modules') as $module => $enabled) {
            if (!$enabled) continue;

            // CSS
            $assetsPath = $this->_config->application->modulesDir . ucfirst($module) . '/Assets/';
            $path = $location . 'css/' . $module . '/';
            FsUtilities::fsCheckLocation($path);
            $cssFiles = glob($assetsPath . 'css/*.less');
            $less->addImportDir($themeDirectory);
            foreach ($cssFiles as $file) {
                $newFileName = $path . basename($file, '.less') . '.css';
                $collectedCss[] = $newFileName;
                $less->checkedCompile($file, $newFileName);
            }

            // JS
            $path = $location . 'js/' . $module . '/';
            FsUtilities::fsCopyRecursive($assetsPath . 'js', $path, true);

            // IMAGES
            $path = $location . 'img/' . $module . '/';
            FsUtilities::fsCopyRecursive($assetsPath . 'img', $path, true);
        }

        ///////////////////////////////////
        // Add css/js into assets manager.
        ///////////////////////////////////
        // css
        foreach ($collectedCss as $css) {
            $this->collection('css')->addCss(str_replace(PUBLIC_PATH . '/', '', $css));
        }

        // js
        $collectedJs = FsUtilities::fsRecursiveGlob($location . 'js', '*.js');
        $sortedJs = array();
        foreach ($collectedJs as $file) {
            $sortedJs[basename($file)] = $file;
        }

        ksort($sortedJs);
        foreach ($sortedJs as $js) {
            $this->collection('js')->addJs(str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $js)));
        }
    }

    /**
     * Compile all assets (css/js).
     *
     * @return void
     */
    public function compileAssets()
    {
        $modules = $this->_di->get('modules');
        $location = $this->_config->application->assets->local;

        /////////////////////////////////////////
        // CSS
        /////////////////////////////////////////
        $themeDirectory = PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme') . '/';
        $outputPath = $location . 'style.css';

        $less = new Less();
        $less->addImportDir($themeDirectory);
        $less->addDir($themeDirectory);

        // modules style files
        foreach ($modules as $module => $enabled) {
            if (!$enabled) continue;
            $less->addDir(ROOT_PATH . '/app/modules/' . ucfirst($module) . '/Assets/css/');
        }

        // compile
        $less->compileTo($outputPath);


        /////////////////////////////////////////
        // JS
        /////////////////////////////////////////
        $outputPath = $location . 'javascript.js';
        file_put_contents($outputPath, "");
        $jsFilter = new Jsmin();
        $files = array();

        foreach ($modules as $module => $enabled) {
            if (!$enabled) continue;

            $files = array_merge($files, glob(ROOT_PATH . '/app/modules/' . ucfirst($module) . '/Assets/js/*.js'));
        }

        $sortedFiles = array();
        foreach ($files as $file) {
            $sortedFiles[basename($file)] = $file;
        }

        ksort($sortedFiles);
        foreach ($sortedFiles as $name => $file) {
            $jsBody = '/*========================================================/' . PHP_EOL;
            $jsBody .= '/ ' . $name . PHP_EOL;
            $jsBody .= '/*========================================================/' . PHP_EOL;
            $jsBody .= $jsFilter->filter(file_get_contents($file)) . PHP_EOL . PHP_EOL;
            file_put_contents($outputPath, $jsBody, FILE_APPEND);
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
            $this->compileAssets();
        }
    }

}