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

class Application
{
    /**
     * @var \Phalcon\DiInterface
     */
    private $_di;

    /**
     * @var \Phalcon\Config
     */
    private $_config;

    /**
     * @var \Phalcon\Mvc\Application
     */
    private $_application;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = include_once(__DIR__ . "/../../config/config.php");

        $this->_di = new \Phalcon\DI\FactoryDefault();

        // Store it in the Di container
        $this->_di->setShared('config', $this->_config);
    }

    /**
     * Runs the application performing all initializations
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
            $this->_config->application->defaultModule => true,
            'user' => true,
        );
        $enabledModules = $this->_config->get('modules');
        if (!$enabledModules){
            $enabledModules = array();
        }
        else{
            $enabledModules = $enabledModules->toArray();
        }
        $modules = array_merge($modules, $enabledModules);
        $this->_di->set('modules', function() use($modules){
            return $modules;
        });

        // Create application
        $this->_application = new \Phalcon\Mvc\Application();

        // Init services and engine system
        foreach ($loaders[$mode] as $service) {
            $this->{'init' . $service}($this->_config);
        }

        $this->_application->setDI($this->_di);

        // register enabled modules
        $enabledModules = array();
        if (!empty($modules)) {
            foreach ($modules as $module => $enabled) {
                if (!$enabled) continue;
                $moduleName = ucfirst($module);
                $enabledModules[$module] = array(
                    'className' => $moduleName . '\Bootstrap',
                    'path' => ROOT_PATH . '/app/modules/' . $moduleName . '/Bootstrap.php',
                );
            }

            if (!empty($enabledModules))
                $this->_application->registerModules($enabledModules);
        }

        // Set application event manager
        $eventsManager = new \Phalcon\Events\Manager();
        $this->_application->setEventsManager($eventsManager);

        // Attach event for modules DI
        $eventsManager->attach(
            "application:afterStartModule",
            function ($event, $application) {
                foreach ($application->getModules() as $module => $moduleDefinition) {
                    /** @var BootstrapInterface $moduleDi */
                    $moduleDi = '\\' . ucfirst($module) . '\Bootstrap';

                    if (class_exists($moduleDi))
                        $moduleDi::dependencyInjection($application->getDI());
                }
            }
        );


    }

    public function getOutput()
    {
        return $this->_application->handle()->getContent();
    }

    // Protected functions

    /**
     * Initializes the loader
     *
     * @param \stdClass $config
     */
    protected function initLoader($config)
    {
        // Creates the autoloader
        $di = $this->_di;
        $modules = $di->get('modules');
        foreach ($modules as $module => $enabled) {
            if (!$enabled) continue;
            $modulesNamespaces[ucfirst($module)] = $this->_config->application->modulesDir . ucfirst($module);
        }
        $modulesNamespaces['Engine'] = $config->application->engineDir;
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces($modulesNamespaces);

        if ($this->_config->application->debug) {
            $eventsManager = new \Phalcon\Events\Manager();
            $di = \Phalcon\DI::getDefault();
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
    protected function initEnvironment($config)
    {
        if (!$config->application->debug) {
            set_error_handler(array('\Engine\Error', 'normal'));
            register_shutdown_function(array('\Engine\Error', 'shutdown'));
            set_exception_handler(array('\Engine\Error', 'exception'));
        }
    }

    /**
     * Initializes the baseUrl
     *
     * @param \stdClass $config
     */
    protected function initUrl($config)
    {
        /**
         * The URL component is used to generate all kind of urls in the
         * application
         */
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri($config->application->baseUri);
        $this->_di->set('url', $url);
    }

    /**
     * Initializes Annotations system
     *
     * @param \stdClass $config
     */
    protected function initAnnotations($config)
    {
        $adapter = new \Phalcon\Annotations\Adapter\Memory();
        $this->_di->set('annotations', $adapter, true);
    }

    /**
     * Initializes router system
     *
     * @param \stdClass $config
     */
    protected function initRouter($config)
    {
        $routerCacheKey = 'router_data.cache';
        $router = $this->_di->get('cacheData')->get($routerCacheKey);
        if ($config->application->debug || $router === null) {
            $saveToCache = ($router === null);

            // load all controllers of all modules for routing system
            $modules = $this->_di->get('modules');

            //Use the annotations router
            $router = new \Phalcon\Mvc\Router\Annotations(false);
            $router->setDefaultModule($this->_config->application->defaultModule);
            $router->setDefaultNamespace(ucfirst($this->_config->application->defaultModule) . '\Controller');
            $router->setDefaultController("index");
            $router->setDefaultAction("index");

            $router->add('/:module/:controller/:action', array(
                'module' => 1,
                'controller' => 2,
                'action' => 3,
            ));

            $router->notFound(array('module' => $this->_config->application->defaultModule, 'controller' => 'error', 'action' => 'show404'));

            //Read the annotations from controllers
            foreach ($modules as $module => $enabled) {
                if (!$enabled) continue;

                $files = scandir($config->application->modulesDir . ucfirst($module) . '/Controller'); // get all file names
                foreach ($files as $file) { // iterate files
                    if ($file == "." || $file == "..") continue;

                    $controller = ucfirst($module) . '\Controller\\' . str_replace('Controller.php', '', $file);
                    if (strpos($file, 'Controller.php') !== false) {
                        $router->addModuleResource(strtolower($module), $controller);
                    }
                }

            }

            if ($saveToCache) {
                $this->_di->get('cacheData')->save($routerCacheKey, $router, 2592000); // 30 days cache
            }
        }

        $this->_di->set('router', $router);

    }

    /**
     * Initializes the logger
     *
     * @param \stdClass $config
     */
    protected function initLogger($config)
    {
        if ($config->application->logger->enabled) {
            $this->_di->set('logger', function () use ($config) {
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
    protected function initDatabase($config)
    {
        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->name,
        ));

        if ($config->application->debug) {
            $eventsManager = new \Phalcon\Events\Manager();

            $logger = new \Phalcon\Logger\Adapter\File($config->application->logger->path . "db.log");

            //Listen all the database events
            $eventsManager->attach('db', function ($event, $connection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                }
            });

            $connection->setEventsManager($eventsManager);
        }

        $this->_di->set('db', $connection);

        /**
         * If the configuration specify the use of metadata adapter use it or use memory otherwise
         */
        $this->_di->set('modelsMetadata', function () use ($config) {
            if (!$config->application->debug && isset($config->models->metadata)) {
                $metaDataConfig = $config->models->metadata;
                $metadataAdapter = '\Phalcon\Mvc\Model\Metadata\\' . $metaDataConfig->adapter;
                return new $metadataAdapter();
            } else {
                return new \Phalcon\Mvc\Model\MetaData\Memory();
            }
        }, true);

    }

    /**
     * Initializes the session
     */
    protected function initSession($config)
    {
        if (!isset($config->application->session))
            return;

        $sessionOptions = array(
            'db' => $this->_di->get('db'),
            'table' => $config->application->session->tableName
        );
        if (isset($config->application->session->lifetime)) {
            $sessionOptions['lifetime'] = $config->application->session->lifetime;
        }

        $session = new \Engine\Session\Database($sessionOptions);
        $session->start();
        $this->_di->set('session', $session, true);
    }

    /**
     * Initializes the cache
     *
     * @param \stdClass $config
     */
    protected function initCache($config)
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
            $this->_di->set('viewCache', $viewCache, true);

            // Cache:Output
            $cacheOutput = new $cacheAdapter($frontOutputCache, $backEndOptions);
            $this->_di->set('cacheOutput', $cacheOutput, true);

            // Cache:Data
            $cacheData = new $cacheAdapter($frontDataCache, $backEndOptions);
            $this->_di->set('cacheData', $cacheData, true);

            // Cache:Models
            $cacheModels = new $cacheAdapter($frontDataCache, $backEndOptions);
            $this->_di->set('modelsCache', $cacheModels, true);
        } else {
            // Create a dummy cache for system.
            // System will work correctly and the data will be always current for all adapters
            $dummyCache = new \Engine\Cache\Dummy(null);
            $this->_di->set('viewCache', $dummyCache);
            $this->_di->set('cacheOutput', $dummyCache);
            $this->_di->set('cacheData', $dummyCache);
            $this->_di->set('modelsCache', $dummyCache);
        }
    }

    /**
     * Initializes the flash messages
     *
     * @param \stdClass $config
     */
    protected function initFlash($config)
    {
        $this->_di->set('flash', function () {
            $flash = new \Phalcon\Flash\Direct(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
            ));
            return $flash;
        });

        $this->_di->set('flashSession', function () {
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
    protected function initEngine($config)
    {
        $di = $this->_di;
        foreach ($di->get('modules') as $module => $enabled) {
            if (!$enabled) continue;

            // initialize module api
            $di->set(strtolower($module), function () use ($module, $di) {
                return new \Engine\Api\Container($module, $di);
            });

        }
    }
}