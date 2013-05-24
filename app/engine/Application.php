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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

use Engine\Package\Exception;

/**
 * @property \Phalcon\DiInterface $_dependencyInjector
 */
class Application extends \Phalcon\Mvc\Application
{

    /**
     * @var \Phalcon\Config
     */
    private $_config;

    public static $defaultModule = 'core';

    /**
     * Constructor
     */
    public function __construct()
    {
        $di = new \Phalcon\DI\FactoryDefault();

        // Store config in the Di container
        $this->_config = include_once(ROOT_PATH . "/app/config/config.php");
        $di->setShared('config', $this->_config);

        parent::__construct($di);
    }

    /**
     * Runs the application, performing all initializations
     *
     * @return mixed
     */
    public function run($mode = 'normal')
    {
        $loaders = array(
            'normal' => array(
                'logger',
                'loader',
                'environment',
                'url',
                'cache',
                'annotations',
                'router',
                'database',
                'session',
                'flash',
                'engine'
            ),
            'mini' => array(
                'logger',
                'loader',
                'database',
                'session'
            )
        );

        if (empty($loaders[$mode]))
            $mode = 'normal';

        // add default module and engine modules
        $modules = array(
            self::$defaultModule => true,
            'user' => true,
        );

        $enabledModules = $this->_config->get('modules');
        if (!$enabledModules) {
            $enabledModules = array();
        } else {
            $enabledModules = $enabledModules->toArray();
        }

        $di = $this->_dependencyInjector;
        $config = $this->_config;

        $modules = array_merge($modules, $enabledModules);

        $di->set('modules', function () use ($modules) {
            return $modules;
        });

        // Set application event manager
        $eventsManager = new \Phalcon\Events\Manager();

        // Init services and engine system
        foreach ($loaders[$mode] as $service) {
            $this->{'init' . $service}($di, $config, $eventsManager);
        }

        // register enabled modules
        $enabledModules = array();
        if (!empty($modules)) {
            foreach ($modules as $module => $enabled) {
                if (!$enabled) {
                    continue;
                }
                $moduleName = ucfirst($module);
                $enabledModules[$module] = array(
                    'className' => $moduleName . '\Bootstrap',
                    'path' => ROOT_PATH . '/app/modules/' . $moduleName . '/Bootstrap.php',
                );
            }

            if (!empty($enabledModules)) {
                $this->registerModules($enabledModules);
            }
        }

        // Attach event for modules DI
        $eventsManager->attach("application:afterStartModule", function ($event, $application) use($config) {
                foreach ($application->getModules() as $module => $moduleDefinition) {
                    /** @var BootstrapInterface $moduleDi */
                    $moduleDi = '\\' . ucfirst($module) . '\Bootstrap';

                    if (class_exists($moduleDi)) {
                        $moduleDi::dependencyInjection($application->getDI(), $config);
                    }
                }
            }
        );


        // Set default services to the DI
        EventsManager::initEngineEvents($eventsManager, $config);
        $this->setEventsManager($eventsManager);
        $di->setShared('eventsManager', $eventsManager);
        $di->setShared('app', $this);

    }

    public function getOutput()
    {
        return $this->handle()->getContent();
    }

    // Protected functions

    /**
     * Initializes the loader
     *
     * @param \stdClass $config
     */
    protected function initLoader($di, $config, $eventsManager)
    {
        $modules = $di->get('modules');
        foreach ($modules as $module => $enabled) {
            if (!$enabled) {
                continue;
            }
            $modulesNamespaces[ucfirst($module)] = $this->_config->application->modulesDir . ucfirst($module);
        }

        $modulesNamespaces['Engine'] = $config->application->engineDir;
        $modulesNamespaces['Plugin'] = $config->application->pluginsDir;
        $modulesNamespaces['Widget'] = $config->application->widgetsDir;
        $modulesNamespaces['Library'] = $config->application->librariesDir;

        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces($modulesNamespaces);

        if ($config->application->debug) {
            $eventsManager->attach('loader', function ($event, $loader, $className) use ($di) {
                if ($event->getType() == 'afterCheckClass') {
                    $di->get('logger')->error("Can't load class '" . $className . "'");
                }
            });
            $loader->setEventsManager($eventsManager);
        }

        $loader->register();

        $di->set('loader', $loader);
    }

    /**
     * Initializes the environment
     *
     * @param \stdClass $config
     */
    protected function initEnvironment($di, $config)
    {
        set_error_handler(array('\Engine\Error', 'normal'));
        register_shutdown_function(array('\Engine\Error', 'shutdown'));
        set_exception_handler(array('\Engine\Error', 'exception'));

        if ($config->application->debug && $config->application->profiler){
            $profiler = new Profiler();
            $di->set('profiler', $profiler);
        }
    }

    /**
     * Initializes the baseUrl
     *
     * @param \stdClass $config
     */
    protected function initUrl($di, $config, $eventsManager)
    {
        /**
         * The URL component is used to generate all kind of urls in the
         * application
         */
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri($config->application->baseUri);
        $di->set('url', $url);
    }

    /**
     * Initializes Annotations system
     *
     * @param \stdClass $config
     */
    protected function initAnnotations($di, $config, $eventsManager)
    {
        $di->set('annotations', function () use ($config) {
            if (!$config->application->debug && isset($config->annotations)) {
                $annotationsAdapter = '\Phalcon\Annotations\Adapter\\' . $config->annotations->adapter;
                $adapter = new $annotationsAdapter($config->annotations->toArray());
            } else {
                $adapter = new \Phalcon\Annotations\Adapter\Memory();
            }

            return $adapter;
        }, true);
    }

    /**
     * Initializes router system
     *
     * @param \stdClass $config
     */
    protected function initRouter($di, $config, $eventsManager)
    {
        $routerCacheKey = 'router_data.cache';

        $cacheData = $di->get('cacheData');
        $router = $cacheData->get($routerCacheKey);

        if ($config->application->debug || $router === null) {

            $saveToCache = ($router === null);

            // load all controllers of all modules for routing system
            $modules = $di->get('modules');

            //Use the annotations router
            $router = new \Phalcon\Mvc\Router\Annotations(false);
            $router->setDefaultModule(self::$defaultModule);
            $router->setDefaultNamespace(ucfirst(self::$defaultModule) . '\Controller');
            $router->setDefaultController("index");
            $router->setDefaultAction("index");

            $router->add('/:module/:controller/:action', array(
                'module' => 1,
                'controller' => 2,
                'action' => 3,
            ));

            $router->notFound(array(
                'module' => self::$defaultModule,
                'namespace' => ucfirst(\Engine\Application::$defaultModule) . '\Controller',
                'controller' => 'error',
                'action' => 'show404'
            ));

            //Read the annotations from controllers
            foreach ($modules as $module => $enabled) {

                if (!$enabled) {
                    continue;
                }

                $files = scandir($config->application->modulesDir . ucfirst($module) . '/Controller'); // get all file names
                foreach ($files as $file) { // iterate files

                    if ($file == "." || $file == "..") {
                        continue;
                    }

                    $controller = ucfirst($module) . '\Controller\\' . str_replace('Controller.php', '', $file);
                    if (strpos($file, 'Controller.php') !== false) {
                        $router->addModuleResource(strtolower($module), $controller);
                    }
                }

            }

            if ($saveToCache) {
                $cacheData->save($routerCacheKey, $router, 2592000); // 30 days cache
            }
        }

        $di->set('router', $router);
    }

    /**
     * Initializes the logger
     *
     * @param \stdClass $config
     */
    protected function initLogger($di, $config, $eventsManager)
    {
        if ($config->application->logger->enabled) {
            $di->set('logger', function () use ($config) {
                $logger = new \Phalcon\Logger\Adapter\File($config->application->logger->path . "main.log");
                $formatter = new \Phalcon\Logger\Formatter\Line($config->application->logger->format);
                $logger->setFormatter($formatter);
                return $logger;
            });
        }
    }

    /**
     * Initializes the database and metadata adapter
     *
     * @param \stdClass $config
     */
    protected function initDatabase($di, $config, $eventsManager)
    {
        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->name,
        ));



        if ($config->application->debug) {
            // Attach logger & profiler
            $logger = new \Phalcon\Logger\Adapter\File($config->application->logger->path . "db.log");
            $profiler = new \Phalcon\Db\Profiler();

            $eventsManager->attach('db', function ($event, $connection) use ($logger, $profiler) {
                if ($event->getType() == 'beforeQuery') {
                    $statement = $connection->getSQLStatement();
                    $logger->log($statement, \Phalcon\Logger::INFO);
                    $profiler->startProfile($statement);
                }
                if ($event->getType() == 'afterQuery') {
                    //Stop the active profile
                    $profiler->stopProfile();
                }
            });

            if ($config->application->profiler && $di->has('profiler')){
                $di->get('profiler')->setDbProfiler($profiler);
            }
            $connection->setEventsManager($eventsManager);
        }

        $di->set('db', $connection);

        $di->set('modelsManager', function () use ($config, $eventsManager) {
            $modelsManager = new \Phalcon\Mvc\Model\Manager();
            $modelsManager->setEventsManager($eventsManager);

            //Attach a listener to models-manager
            $eventsManager->attach('modelsManager', new \Engine\Model\AnnotationsInitializer());

            return $modelsManager;
        }, true);

        /**
         * If the configuration specify the use of metadata adapter use it or use memory otherwise
         */
        $di->set('modelsMetadata', function () use ($config) {
            if (!$config->application->debug && isset($config->metadata)) {
                $metaDataConfig = $config->metadata;
                $metadataAdapter = '\Phalcon\Mvc\Model\Metadata\\' . $metaDataConfig->adapter;
                $metaData = new $metadataAdapter($config->metadata->toArray());
            } else {
                $metaData = new \Phalcon\Mvc\Model\MetaData\Memory();
            }

            $metaData->setStrategy(new \Engine\Model\AnnotationsMetaDataInitializer());
            return $metaData;
        }, true);

    }

    /**
     * Initializes the session
     */
    protected function initSession($di, $config, $eventsManager)
    {
        if (!isset($config->application->session)) {
            $session = new \Phalcon\Session\Adapter\Files();
        } else {
            $sessionOptions = array(
                'db' => $di->get('db'),
                'table' => $config->application->session->tableName
            );

            if (isset($config->application->session->lifetime)) {
                $sessionOptions['lifetime'] = $config->application->session->lifetime;
            }

            $session = new \Engine\Session\Database($sessionOptions);
        }
        $session->start();
        $di->set('session', $session, true);
    }

    /**
     * Initializes the cache
     *
     * @param \stdClass $config
     */
    protected function initCache($di, $config, $eventsManager)
    {

        if (!$config->application->debug) {

            // Get the parameters
            $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
            $frontEndOptions = array('lifetime' => $config->application->cache->lifetime);
            $backEndOptions = $config->application->cache->toArray();
            $frontOutputCache = new \Phalcon\Cache\Frontend\Output($frontEndOptions);
            $frontDataCache = new \Phalcon\Cache\Frontend\Data($frontEndOptions);

            // Cache:View
            $viewCache = new $cacheAdapter($frontOutputCache, $backEndOptions);
            $di->set('viewCache', $viewCache, false);

            // Cache:Output
            $cacheOutput = new $cacheAdapter($frontOutputCache, $backEndOptions);
            $di->set('cacheOutput', $cacheOutput, true);

            // Cache:Data
            $cacheData = new $cacheAdapter($frontDataCache, $backEndOptions);
            $di->set('cacheData', $cacheData, true);

            // Cache:Models
            $cacheModels = new $cacheAdapter($frontDataCache, $backEndOptions);
            $di->set('modelsCache', $cacheModels, true);

        } else {
            // Create a dummy cache for system.
            // System will work correctly and the data will be always current for all adapters
            $dummyCache = new \Engine\Cache\Dummy(null);
            $di->set('viewCache', $dummyCache);
            $di->set('cacheOutput', $dummyCache);
            $di->set('cacheData', $dummyCache);
            $di->set('modelsCache', $dummyCache);
        }
    }

    /**
     * Initializes the flash messages
     *
     * @param \stdClass $config
     */
    protected function initFlash($di, $config, $eventsManager)
    {
        $di->set('flash', function () {
            $flash = new \Phalcon\Flash\Direct(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
            ));
            return $flash;
        });

        $di->set('flashSession', function () {
            $flash = new \Phalcon\Flash\Session(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
            ));
            return $flash;
        });
    }

    /**
     * Initializes engine services
     *
     * @param \stdClass $config
     */
    protected function initEngine($di, $config, $eventsManager)
    {

        foreach ($di->get('modules') as $module => $enabled) {

            if (!$enabled) {
                continue;
            }

            // initialize module api
            $di->set(strtolower($module), function () use ($module, $di) {
                return new \Engine\Api\Container($module, $di);
            });
        }

        $assetsManager = $di->get('assets');

        if ($this->_config->application->debug) {
            $assetsManager
                ->collection('js')
                ->addJs('external/jquery/jquery-1.8.3.min.js')
                ->addJs('external/jquery/jquery-ui-1.9.0.custom.min.js');

            $this->installAssets($assetsManager, $di, $config);
        } else {
            $remote = $this->_config->application->assets->get('remote');
            if ($remote) {
                $assetsManager
                    ->collection('css')
                    ->setPrefix($remote)
                    ->setLocal(false);
                $assetsManager
                    ->collection('js')
                    ->setPrefix($remote)
                    ->setLocal(false);

            }
            $assetsManager
                ->collection('css')
                ->addCss('assets/style.css');

            $assetsManager
                ->collection('js')
                ->addJs('external/jquery/jquery-1.8.3.min.js')
                ->addJs('external/jquery/jquery-ui-1.9.0.custom.min.js')
                ->addJs('assets/javascript.js');


        }
    }

    /**
     * Install assets from all modules
     */
    public function installAssets($assetsManager = null, $di = null, $config = null)
    {
        if ($di === null) {
            $di = $this->_dependencyInjector;
        }
        if ($assetsManager === null) {
            $assetsManager = $di->get('assets');
        }
        if ($config === null) {
            $config = $this->_config;
        }

        $location = $config->application->assets->local;

        // compile themes css
        $less = \Engine\Css\Less::factory();
        $collectedCss = array();
        $themeDirectory = ROOT_PATH . '/public/themes/' . \Core\Model\Settings::getSetting('system_theme');
        $themeFiles = glob($themeDirectory . '/*.less');
        \Engine\Package\Utilities::fsCheckLocation($location . 'css/');
        foreach ($themeFiles as $file) {
            $newFileName = $location . 'css/' . basename($file, '.less') . '.css';
            $collectedCss[] = $newFileName;
            $less->checkedCompile($file, $newFileName);
        }

        // collect js/img from modules
        foreach ($di->get('modules') as $module => $enabled) {
            if (!$enabled) continue;

            // CSS
            $assetsPath = $config->application->modulesDir . ucfirst($module) . '/Assets/';
            $path = $location . 'css/' . $module . '/';
            \Engine\Package\Utilities::fsCheckLocation($path);
            $cssFiles = glob($assetsPath . 'css/*.less');
            $less->addImportDir($themeDirectory);
            foreach ($cssFiles as $file) {
                $newFileName = $path . basename($file, '.less') . '.css';
                $collectedCss[] = $newFileName;
                $less->checkedCompile($file, $newFileName);
            }

            // JS
            $path = $location . 'js/' . $module . '/';
            \Engine\Package\Utilities::fsCopyRecursive($assetsPath . 'js', $path, true);

            // IMAGES
            $path = $location . 'img/' . $module . '/';
            \Engine\Package\Utilities::fsCopyRecursive($assetsPath . 'img', $path, true);
        }

        // add css/js into assets manager
        // css
        $collection = $assetsManager->collection('css');
        foreach ($collectedCss as $css) {
            $collection->addCss(str_replace(ROOT_PATH . '/public/', '', $css));
        }
        $assetsManager->set('css', $collection);

        // js
        $collection = $assetsManager->collection('js');
        $collectedJs = \Engine\Package\Utilities::fsRecursiveGlob($location . 'js', '*.js');
        $sortedJs = array();
        foreach ($collectedJs as $file) {
            $sortedJs[basename($file)] = $file;
        }

        ksort($sortedJs);
        foreach ($sortedJs as $js) {
            $collection->addJs(str_replace(ROOT_PATH . '/public/', '', $js));
        }
        $assetsManager->set('js', $collection);
    }

    /**
     * Compile all assets (css/js)
     */
    public function compileAssets($di = null, $config = null)
    {
        if ($di === null) {
            $di = $this->_dependencyInjector;
        }
        if ($config === null) {
            $config = $this->_config;
        }

        $modules = $di->get('modules');
        $location = $config->application->assets->local;

        /////////////////////////////////////////
        // CSS
        /////////////////////////////////////////
        $themeDirectory = ROOT_PATH . '/public/themes/' . \Core\Model\Settings::getSetting('system_theme') . '/';
        $outputPath = $location . 'style.css';

        $less = new \Engine\Css\Less();
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
        // @TODO: minify
        $outputPath = $location . 'javascript.js';
        file_put_contents($outputPath, "");
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
        foreach ($sortedFiles as $file) {
            file_put_contents($outputPath, PHP_EOL . PHP_EOL . file_get_contents($file), FILE_APPEND);
        }

    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {

        // clear cache
        $viewCache = $this->_dependencyInjector->get('viewCache');
        $cacheOutput = $this->_dependencyInjector->get('cacheOutput');
        $cacheData = $this->_dependencyInjector->get('cacheData');
        $modelsCache = $this->_dependencyInjector->get('modelsCache');
        $config = $this->_dependencyInjector->get('config');

        $keys = $viewCache->queryKeys();
        foreach ($keys as $key) {
            $viewCache->delete($key);
        }

        $keys = $cacheOutput->queryKeys();
        foreach ($keys as $key) {
            $cacheOutput->delete($key);
        }

        $keys = $cacheData->queryKeys();
        foreach ($keys as $key) {
            $cacheData->delete($key);
        }

        $keys = $modelsCache->queryKeys();
        foreach ($keys as $key) {
            $modelsCache->delete($key);
        }

        // clear files cache
        $files = glob($config->application->cache->cacheDir . '*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                @unlink($file); // delete file
            }
        }

        // clear view cache
        $files = glob($config->application->view->compiledPath . '*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                @unlink($file); // delete file
            }
        }

        // clear metadata cache
        if ($config->metadata && $config->metadata->metaDataDir) {
            $files = glob($config->metadata->metaDataDir . '*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    @unlink($file); // delete file
                }
            }
        }

        // clear annotations cache
        if ($config->annotations && $config->annotations->annotationsDir) {
            $files = glob($config->annotations->annotationsDir . '*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    @unlink($file); // delete file
                }
            }
        }

        // clear assets cache
        $files = \Engine\Package\Utilities::fsRecursiveGlob(ROOT_PATH . '/public/assets/', '*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                @unlink($file); // delete file
        }

        $this->installAssets();
        $this->compileAssets();
    }

    /**
     * Save application config to file
     *
     * @param \Phalcon\Config $config
     */
    public function saveConfig($config = null)
    {
        if ($config === null) {
            $config = $this->_config;
        }
        $configText = var_export($config->toArray(), true);
        $configText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $configText);
        $configText = '<?php

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
* to phalconeye@gmail.com so we can send you a copy immediately.
*
*/

/**
* WARNING
*
* Manual changes to this file may cause a malfunction of the system.
* Be careful when changing settings!
*
*/

return new \\Phalcon\\Config(' . $configText . ');';
        file_put_contents(ROOT_PATH . '/app/config/config.php', $configText);
    }
}