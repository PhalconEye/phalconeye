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

namespace Engine;

use Engine\Api\Injector as ApiInjector;
use Engine\Asset\Manager as AssetsManager;
use Engine\Cache\Dummy;
use Engine\Db\Model\Annotations\Initializer as ModelAnnotationsInitializer;
use Engine\Widget\Catalog;
use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
use Phalcon\Cache\Frontend\Data as CacheData;
use Phalcon\Cache\Frontend\Output as CacheOutput;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Adapter;
use Phalcon\Db\Profiler as DatabaseProfiler;
use Phalcon\DI;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Loader;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Phalcon\Mvc\Application as PhalconApplication;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Strategy\Annotations as StrategyAnnotations;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Annotations as RouterAnnotations;
use Phalcon\Mvc\Url;
use Phalcon\Session\Adapter\Files as SessionFiles;
use Phalcon\Session\Adapter as SessionAdapter;

/**
 * Application class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @TODO      Refactor this.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application extends PhalconApplication
{
    const
        /**
         * Default module.
         */
        SYSTEM_DEFAULT_MODULE = 'core';


    /**
     * Application configuration.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Loaders for different modes.
     *
     * @var array
     */
    private $_loaders =
        [
            'normal' => [
                'logger',
                'loader',
                'environment',
                'cache',
                'annotations',
                'database',
                'router',
                'session',
                'flash',
                'engine'
            ],
            'console' => [
                'logger',
                'loader',
                'database',
                'cache',
                'engine'
            ],
            'session' => [
                'logger',
                'loader',
                'cache',
                'database',
                'session'
            ],
        ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Create default DI.
        $di = new DI\FactoryDefault();

        // Get config.
        $this->_config = Config::factory();

        if (!$this->_config->application->installed) {
            define('CHECK_REQUIREMENTS', true);
            require_once(PUBLIC_PATH . '/requirements.php');
        }

        // Store config in the Di container.
        $di->setShared('config', $this->_config);

        parent::__construct($di);
    }

    /**
     * Runs the application, performing all initializations.
     *
     * @param string $mode Mode name.
     *
     * @return void
     */
    public function run($mode = 'normal')
    {
        if (empty($this->_loaders[$mode])) {
            $mode = 'normal';
        }

        // Set application main objects.
        $di = $this->_dependencyInjector;
        $di->setShared('app', $this);
        $config = $this->_config;
        $eventsManager = new EventsManager();
        $this->setEventsManager($eventsManager);

        // Init services and engine system.
        foreach ($this->_loaders[$mode] as $service) {
            $serviceName = ucfirst($service);
            $eventsManager->fire('init:before' . $serviceName, null);
            $result = $this->{'init' . $serviceName}($di, $config, $eventsManager);
            $eventsManager->fire('init:after' . $serviceName, $result);
        }

        // Set default services to the DI.
        // @TODO do not miss this line. Also them must be before 'init' events.
        $this->_attachEngineEvents($eventsManager, $config);
        $di->setShared('eventsManager', $eventsManager);
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->handle()->getContent();
    }

    /**
     * Init loader.
     *
     * @param DI            $di            Dependency Injection.
     * @param Config        $config        Config object.
     * @param EventsManager $eventsManager Event manager.
     *
     * @return Loader
     */
    protected function initLoader($di, $config, $eventsManager)
    {
        // Add default module and engine modules.
        $modules = array_merge(
            [
                self::SYSTEM_DEFAULT_MODULE => true,
                'user' => true,
            ],
            $this->_config->modules->toArray()
        );
        $di->set(
            'modules',
            function () use ($modules) {
                return $modules;
            }
        );

        // Add all required namespaces and modules.
        $modulesNamespaces = [];
        $bootstraps = [];
        foreach ($modules as $module => $enabled) {
            if (!$enabled) {
                continue;
            }
            $moduleName = ucfirst($module);

            $modulesNamespaces[$moduleName] = $this->_config->directories->modules . $moduleName;
            $bootstraps[$module] = $moduleName . '\Bootstrap';
        }

        $modulesNamespaces['Engine'] = $config->directories->engine;
        $modulesNamespaces['Plugin'] = $config->directories->plugins;
        $modulesNamespaces['Widget'] = $config->directories->widgets;
        $modulesNamespaces['Library'] = $config->directories->libraries;

        $loader = new Loader();
        $loader->registerNamespaces($modulesNamespaces);

        if ($config->application->debug && $config->application->installed) {
            $eventsManager->attach(
                'loader',
                function ($event, $loader, $className) use ($di) {
                    if ($event->getType() == 'afterCheckClass') {
                        $di->get('logger')->error("Can't load class '" . $className . "'");
                    }
                }
            );
            $loader->setEventsManager($eventsManager);
        }

        $loader->register();
        $this->registerModules($bootstraps);
        $di->set('loader', $loader);

        return $loader;
    }

    /**
     * Init environment.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return Url
     */
    protected function initEnvironment($di, $config)
    {
        set_error_handler(['\Engine\Exception', 'normal']);
        register_shutdown_function(['\Engine\Exception', 'shutdown']);
        set_exception_handler(['\Engine\Exception', 'exception']);

        if ($config->application->debug && $config->application->profiler && $config->application->installed) {
            $profiler = new Profiler();
            $di->set('profiler', $profiler);
        }

        /**
         * The URL component is used to generate all kind of urls in the
         * application
         */
        $url = new Url();
        $url->setBaseUri($config->application->baseUri);
        $di->set('url', $url);

        return $url;
    }

    /**
     * Init annotations.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return void
     */
    protected function initAnnotations($di, $config)
    {
        $di->set(
            'annotations',
            function () use ($config) {
                if (!$config->application->debug && isset($config->annotations)) {
                    $annotationsAdapter = '\Phalcon\Annotations\Adapter\\' . $config->annotations->adapter;
                    $adapter = new $annotationsAdapter($config->annotations->toArray());
                } else {
                    $adapter = new AnnotationsMemory();
                }

                return $adapter;
            },
            true
        );
    }

    /**
     * Init router.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return Router
     */
    protected function initRouter($di, $config)
    {
        $defaultModuleName = ucfirst(self::SYSTEM_DEFAULT_MODULE);

        // Check installation.
        if (!$config->application->installed) {
            $router = new RouterAnnotations(false);

            $router->setDefaultModule(self::SYSTEM_DEFAULT_MODULE);
            $router->setDefaultNamespace($defaultModuleName . '\Controller');
            $router->setDefaultController("Install");
            $router->setDefaultAction("index");
            $router->addModuleResource(self::SYSTEM_DEFAULT_MODULE, $defaultModuleName . '\Controller\Install');
            $di->set('installationRequired', true);
            $di->set('router', $router);

            return;
        }

        $routerCacheKey = 'router_data.cache';

        $cacheData = $di->get('cacheData');
        $router = $cacheData->get($routerCacheKey);

        if ($config->application->debug || $router === null) {

            $saveToCache = ($router === null);

            // load all controllers of all modules for routing system
            $modules = $di->get('modules');

            //Use the annotations router
            $router = new \Phalcon\Mvc\Router\Annotations(false);
            $router->setDefaultModule(self::SYSTEM_DEFAULT_MODULE);
            $router->setDefaultNamespace(ucfirst(self::SYSTEM_DEFAULT_MODULE) . '\Controller');
            $router->setDefaultController("Index");
            $router->setDefaultAction("index");

            $router->add(
                '/:module/:controller/:action',
                [
                    'module' => 1,
                    'controller' => 2,
                    'action' => 3,
                ]
            );

            $router->notFound(
                [
                    'module' => self::SYSTEM_DEFAULT_MODULE,
                    'namespace' => ucfirst(self::SYSTEM_DEFAULT_MODULE) . '\Controller',
                    'controller' => 'Error',
                    'action' => 'show404'
                ]
            );

            //Read the annotations from controllers
            foreach ($modules as $module => $enabled) {
                if (!$enabled) {
                    continue;
                }
                $moduleName = ucfirst($module);

                // Get all file names.
                $files = scandir($config->directories->modules . $moduleName . '/Controller');

                // Iterate files.
                foreach ($files as $file) {
                    if ($file == "." || $file == ".." || strpos($file, 'Controller.php') === false) {
                        continue;
                    }

                    $controller = $moduleName . '\Controller\\' . str_replace('Controller.php', '', $file);
                    $router->addModuleResource(strtolower($module), $controller);
                }
            }
            if ($saveToCache) {
                $cacheData->save($routerCacheKey, $router, 2592000); // 30 days cache
            }
        }

        $di->set('router', $router);

        return $router;
    }

    /**
     * Init logger.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return void
     */
    protected function initLogger($di, $config)
    {
        if ($config->application->logger->enabled) {
            $di->set(
                'logger',
                function ($file = 'main', $format = null) use ($config) {
                    $logger = new File($config->application->logger->path . APPLICATION_STAGE . '.' . $file . '.log');
                    $formatter = new FormatterLine(($format ? $format : $config->application->logger->format));
                    $logger->setFormatter($formatter);

                    return $logger;
                },
                false
            );
        }
    }

    /**
     * Init database.
     *
     * @param DI            $di            Dependency Injection.
     * @param Config        $config        Config object.
     * @param EventsManager $eventsManager Event manager.
     *
     * @return Pdo
     */
    protected function initDatabase($di, $config, $eventsManager)
    {
        if (!$config->application->installed) {
            return;
        }

        $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        /** @var Pdo $connection */
        $connection = new $adapter(
            [
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
            ]
        );

        if ($config->application->debug) {
            // Attach logger & profiler.
            $logger = new File($config->application->logger->path . "db.log");
            $profiler = new DatabaseProfiler();

            $eventsManager->attach(
                'db',
                function ($event, $connection) use ($logger, $profiler) {
                    if ($event->getType() == 'beforeQuery') {
                        $statement = $connection->getSQLStatement();
                        $logger->log($statement, Logger::INFO);
                        $profiler->startProfile($statement);
                    }
                    if ($event->getType() == 'afterQuery') {
                        // Stop the active profile.
                        $profiler->stopProfile();
                    }
                }
            );

            if ($config->application->profiler && $di->has('profiler')) {
                $di->get('profiler')->setDbProfiler($profiler);
            }
            $connection->setEventsManager($eventsManager);
        }

        $di->set('db', $connection);
        $di->set(
            'modelsManager',
            function () use ($config, $eventsManager) {
                $modelsManager = new ModelsManager();
                $modelsManager->setEventsManager($eventsManager);

                // Attach a listener to models-manager
                $eventsManager->attach('modelsManager', new ModelAnnotationsInitializer());

                return $modelsManager;
            },
            true
        );

        /**
         * If the configuration specify the use of metadata adapter use it or use memory otherwise.
         */
        $di->set(
            'modelsMetadata',
            function () use ($config) {
                if (!$config->application->debug && isset($config->application->metadata)) {
                    $metaDataConfig = $config->application->metadata;
                    $metadataAdapter = '\Phalcon\Mvc\Model\Metadata\\' . $metaDataConfig->adapter;
                    $metaData = new $metadataAdapter($config->application->metadata->toArray());
                } else {
                    $metaData = new \Phalcon\Mvc\Model\MetaData\Memory();
                }

                $metaData->setStrategy(new StrategyAnnotations());

                return $metaData;
            },
            true
        );

        return $connection;
    }

    /**
     * Init session.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return SessionAdapter
     */
    protected function initSession($di, $config)
    {
        if (!isset($config->application->session)) {
            $session = new SessionFiles();
        } else {
            $adapterClass = 'Phalcon\Session\Adapter\\' . $config->application->session->adapter;
            $session = new $adapterClass($config->application->session->toArray());
        }
        $session->start();
        $di->set('session', $session, true);

        return $session;
    }

    /**
     * Init cache.
     *
     * @param DI     $di     Dependency Injection.
     * @param Config $config Config object.
     *
     * @return void
     */
    protected function initCache($di, $config)
    {
        if (!$config->application->debug) {
            // Get the parameters.
            $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
            $frontEndOptions = ['lifetime' => $config->application->cache->lifetime];
            $backEndOptions = $config->application->cache->toArray();
            $frontOutputCache = new CacheOutput($frontEndOptions);
            $frontDataCache = new CacheData($frontEndOptions);

            // Cache:View.
            $viewCache = new $cacheAdapter($frontOutputCache, $backEndOptions);
            $di->set('viewCache', $viewCache, false);

            // Cache:Output.
            $cacheOutput = new $cacheAdapter($frontOutputCache, $backEndOptions);
            $di->set('cacheOutput', $cacheOutput, true);

            // Cache:Data.
            $cacheData = new $cacheAdapter($frontDataCache, $backEndOptions);
            $di->set('cacheData', $cacheData, true);

            // Cache:Models.
            $cacheModels = new $cacheAdapter($frontDataCache, $backEndOptions);
            $di->set('modelsCache', $cacheModels, true);

        } else {
            // Create a dummy cache for system.
            // System will work correctly and the data will be always current for all adapters.
            $dummyCache = new Dummy(null);
            $di->set('viewCache', $dummyCache);
            $di->set('cacheOutput', $dummyCache);
            $di->set('cacheData', $dummyCache);
            $di->set('modelsCache', $dummyCache);
        }
    }

    /**
     * Init flash messages.
     *
     * @param DI $di Dependency Injection.
     *
     * @return void
     */
    protected function initFlash($di)
    {
        $di->set(
            'flash',
            function () {
                $flash = new FlashDirect(
                    [
                        'error' => 'alert alert-error',
                        'success' => 'alert alert-success',
                        'notice' => 'alert alert-info',
                    ]
                );

                return $flash;
            }
        );

        $di->set(
            'flashSession',
            function () {
                $flash = new FlashSession(
                    [
                        'error' => 'alert alert-error',
                        'success' => 'alert alert-success',
                        'notice' => 'alert alert-info',
                    ]
                );

                return $flash;
            }
        );
    }

    /**
     * Init engine.
     *
     * @param DI $di Dependency Injection.
     *
     * @return void
     */
    protected function initEngine($di)
    {
        foreach ($di->get('modules') as $module => $enabled) {
            if (!$enabled) {
                continue;
            }

            // Initialize module api.
            $di->setShared(
                strtolower($module),
                function () use ($module, $di) {
                    return new ApiInjector($module, $di);
                }
            );
        }

        $di->setShared(
            'transactions',
            function () {
                return new TxManager();
            }
        );
        $di->setShared('assets', new AssetsManager($di));
        $di->setShared('widgets', new Catalog());
    }

    /**
     * Clear application cache.
     *
     * @return void
     */
    public function clearCache()
    {
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

        // Files deleter helper.
        $deleteFiles = function ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        };

        // Clear files cache.
        $deleteFiles(glob($config->application->cache->path . '*'));

        // Clear view cache.
        $deleteFiles(glob($config->application->view->compiledPath . '*'));

        // Clear metadata cache.
        if ($config->application->metadata && $config->application->metadata->path) {
            $deleteFiles(glob($config->application->metadata->path . '*'));
        }

        // Clear annotations cache.
        if ($config->application->annotations && $config->application->annotations->path) {
            $deleteFiles(glob($config->application->annotations->path . '*'));
        }

        // Clear assets.
        $this->_dependencyInjector->getShared('assets')->clear();
    }

    /**
     * Init modules and register them.
     *
     * @param array $modules Modules bootstrap classes.
     * @param null  $merge   Merge with existing.
     *
     * @return $this
     */
    public function registerModules($modules, $merge = null)
    {
        $bootstraps = [];
        $di = $this->getDI();
        foreach ($modules as $moduleName => $moduleClass) {
            if (isset($this->_modules[$moduleName])) {
                continue;
            }

            $bootstrap = new $moduleClass($di, $this->getEventsManager());
            $bootstraps[$moduleName] = function () use ($bootstrap, $di) {
                $bootstrap->registerServices();

                return $bootstrap;
            };
        }

        return parent::registerModules($bootstraps, $merge);
    }

    /**
     * Attach required events.
     *
     * @param EventsManager $eventsManager Events manager object.
     * @param Config        $config        Application configuration.
     *
     * @return void
     */
    protected function _attachEngineEvents($eventsManager, $config)
    {
        // Attach modules plugins events.
        $events = $config->get('events')->toArray();

        try {
            foreach ($events as $class => $event) {
                $eventsManager->attach($event, new $class());
            }
        } catch (\Exception $e) {
            $this->getDI()->getLogger()->error('There is a problem attaching engine event:');
            Exception::exception($e);
        }
//
//        $loadedModules = $config->modules->toArray();
//        if (!empty($modules)) {
//            foreach ($modules as $module => $events) {
//                if (!in_array($module, $loadedModules)) {
//                    continue;
//                }
//                foreach ($events as $event) {
//                    $pluginClass = $event['namespace'] . '\\' . $event['class'];
//                    $eventsManager->attach($event['type'], new $pluginClass());
//                }
//            }
//        }
//
//        // Attach plugins events.
//        $plugins = $config->get('plugins');
//        if (!empty($plugins)) {
//            foreach ($plugins as $pluginName => $plugin) {
//
//                if (!$plugin['enabled'] || empty($plugin['events'])) {
//                    continue;
//                }
//
//                $pluginClass = '\Plugin\\' . ucfirst($pluginName) . '\\' . ucfirst($pluginName);
//                $pluginObject = new $pluginClass();
//                foreach ($plugin['events'] as $event) {
//                    $eventsManager->attach($event, $pluginObject);
//                }
//            }
//        }
    }

    /**
     * Check if application is used from console.
     *
     * @return bool
     */
    public function isConsole()
    {
        return (php_sapi_name() == 'cli');
    }
}