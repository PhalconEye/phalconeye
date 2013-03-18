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


class Application
{
    /**
     * @var \Phalcon\DiInterface
     */
    private $_di;

    private $_config;

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
    public function run()
    {
        $loaders = array(
            'logger',
            'loader',
            'environment',
            'url',
            'cache',
            'router',
            'view',
            'database',
            'session',
            'acl',
            'dispatcher',
            'flash',
            'engine'
        );
        foreach ($loaders as $service) {
            $this->{'init' . $service}($this->_config);
        }

        $application = new \Phalcon\Mvc\Application();
        $application->setDI($this->_di);

        return $application->handle()->getContent();

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
        $loader = new \Phalcon\Loader();
        $di = $this->_di;

        // Register the namespaces
        $loader
            ->registerDirs(array(
                $config->application->engineDir,
                $config->application->controllersDir,
                $config->application->modelsDir,
                $config->application->miscDir
            ));

        if ($config->application->debug) {
            $eventsManager = new \Phalcon\Events\Manager();
            $eventsManager->attach('loader', function ($event, $loader, $className) use ($di) {
                if ($event->getType() == 'afterCheckClass') {
                    $di->get('logger')->log("Can't load class '" . $className . "'", \Phalcon\Logger::ERROR);
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
            set_error_handler(array('Error', 'normal'));
            register_shutdown_function(array('Error', 'shutdown'));
            set_exception_handler(array('Error', 'exception'));
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
        $this->_di->set('url', function () use ($config) {
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });
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
            $saveToCache = false;
            if ($router === null)
                $saveToCache = true;

            //Use the annotations router
            $router = new \Phalcon\Mvc\Router\Annotations(false);

            $router->add('/:controller/:action', array(
                'controller' => 1,
                'action' => 2,
            ));

            $router->notFound(array('controller' => 'error', 'action' => 'show404'));

            //Read the annotations from controllers
            $files = scandir($config->application->controllersDir); // get all file names

            foreach ($files as $file) { // iterate files
                if ($file == "." || $file == "..") continue;
                if (strpos($file, 'Controller.php') !== false)
                    $router->addResource(str_replace('Controller.php', '', $file));
            }

            if ($saveToCache) {
                $this->_di->get('cacheData')->save($routerCacheKey, $router, 2592000); // 30 days cache
            }
        }

        $this->_di->set('router', $router);

    }

    /**
     * Initializes the view
     *
     * @param \stdClass $config
     */
    protected function initView($config)
    {
        $di = $this->_di;
        $di->set('view', function () use ($config) {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir($config->application->viewsDir);

            $view->registerEngines(array(
                ".volt" => function ($view, \Phalcon\DiInterface $di) use ($config) {
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                    $volt->setOptions(array(
                        "compiledPath" => $config->application->view->compiledPath,
                        "compiledExtension" => $config->application->view->compiledExtension,
                        'compileAlways' => $config->application->debug
                    ));


                    $compiler = $volt->getCompiler();

                    //register helper
                    $compiler->addFunction('helper', function ($resolvedArgs, $exprArgs) {
                        $name = $exprArgs[0]['expr']['value'];

                        $resolvedArgs = explode(', ', $resolvedArgs);
                        unset($resolvedArgs[0]);
                        $resolvedArgs = implode(', ', $resolvedArgs);
                        return 'Helper_' . ucfirst($name) . '::_(' . $resolvedArgs . ')';
                    });

                    // current viewer helper
                    $compiler->addFunction('viewer', function () {
                        return 'User::getViewer()';
                    });

                    // register translation filter
                    $compiler->addFilter('trans', function ($resolvedArgs) {
                        return 'Helper_Translate::_(' . $resolvedArgs . ')';
                    });


                    return $volt;
                }
            ));

            return $view;
        });
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
                $formatter = new Phalcon\Logger\Formatter\Line($config->application->logger->format);
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
        $this->_di->set('db', function () use ($config) {
            $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->name,
            ));

            if ($config->application->debug) {
                $eventsManager = new Phalcon\Events\Manager();

                $logger = new \Phalcon\Logger\Adapter\File($config->application->logger->path . "db.log");

                //Listen all the database events
                $eventsManager->attach('db', function ($event, $connection) use ($logger) {
                    if ($event->getType() == 'beforeQuery') {
                        $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                });

                $connection->setEventsManager($eventsManager);
            }

            return $connection;
        });

        /**
         * If the configuration specify the use of metadata adapter use it or use memory otherwise
         */
        $this->_di->set('modelsMetadata', function () use ($config) {
            if (isset($config->models->metadata)) {
                $metaDataConfig = $config->models->metadata;
                $metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\' . $metaDataConfig->adapter;
                return new $metadataAdapter();
            } else {
                return new PhMetadataMemory();
            }
        }, true);

    }

    /**
     * Initializes the session
     */
    protected function initSession()
    {
        $this->_di->set('session', function () {
            $session = new \Phalcon\Session\Adapter\Files();
            $session->start();
            return $session;
        }, true);
    }

    protected function initAcl($config)
    {
        $di = $this->_di;
        $di->set('acl', function () use ($di) {
            return new Api_Acl($di);
        });
    }


    protected function initDispatcher($config)
    {
        $di = $this->_di;
        $di->set('dispatcher', function () use ($di, $config) {
            $eventsManager = $di->getShared('eventsManager');
            if (!$config->application->debug) {
                $eventsManager->attach(
                    "dispatch:beforeException", function ($event, $dispatcher, $exception) {
                    //The controller exists but the action not
                    if ($event->getType() == 'beforeNotFoundAction') {
                        $dispatcher->forward(array(
                            'controller' => 'error',
                            'action' => 'show404'
                        ));
                        return false;
                    }

                    //Alternative way, controller or action doesn't exist
                    if ($event->getType() == 'beforeException') {
                        switch ($exception->getCode()) {
                            case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                            case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                                $dispatcher->forward(array(
                                    'controller' => 'error',
                                    'action' => 'show404'
                                ));
                                return false;
                        }
                    }
                });

                $eventsManager->attach('dispatch', new Plugin_CacheAnnotation());
            }

            /**
             * Listening to events in the dispatcher using the
             * Acl plugin
             */
            $eventsManager->attach('dispatch', $di->get('acl'));

            // Create dispatcher
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });
    }


    /**
     * Initializes the cache
     *
     * @param \stdClass $config
     */
    protected function initCache($config)
    {
        if (!$config->application->debug) {
            $this->_di->set('viewCache', function () use ($config) {
                // Get the parameters
                $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
                $frontEndOptions = array('lifetime' => $config->application->cache->lifetime);
                $backEndOptions = $config->application->cache->toArray();

                $frontCache = new \Phalcon\Cache\Frontend\Output($frontEndOptions);
                $cache = new $cacheAdapter($frontCache, $backEndOptions);

                return $cache;
            });

            $this->_di->set('cacheOutput', function () use ($config) {
                // Get the parameters
                $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
                $frontEndOptions = array('lifetime' => $config->application->cache->lifetime);
                $backEndOptions = $config->application->cache->toArray();

                $frontCache = new \Phalcon\Cache\Frontend\Output($frontEndOptions);
                $cache = new $cacheAdapter($frontCache, $backEndOptions);

                return $cache;
            });

            $this->_di->set('cacheData', function () use ($config) {
                // Get the parameters
                $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
                $frontEndOptions = array('lifetime' => $config->application->cache->lifetime);
                $backEndOptions = $config->application->cache->toArray();

                $frontCache = new \Phalcon\Cache\Frontend\Data($frontEndOptions);
                $cache = new $cacheAdapter($frontCache, $backEndOptions);

                return $cache;
            });

            $this->_di->set('modelsCache', function () use ($config) {
                // Get the parameters
                $cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->application->cache->adapter;
                $frontEndOptions = array('lifetime' => $config->application->cache->lifetime);
                $backEndOptions = $config->application->cache->toArray();

                $frontCache = new \Phalcon\Cache\Frontend\Data($frontEndOptions);
                $cache = new $cacheAdapter($frontCache, $backEndOptions);

                return $cache;
            });
        } else {
            // Create a dummy cache for system.
            // System will work correctly and the data will be always current for all adapters
            $dummyCache = new Cache_Dummy(null);
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
        $this->_di->set('auth', function () use ($di) {
            return new Api_Auth($di);
        });

        $locale = $di->get('session')->get('locale', Settings::getSetting('system_default_language'));

        $translate = null;

        if (!$config->application->debug) {
            $messages = array();
            if (file_exists(ROOT_PATH . "/app/var/languages/" . $locale . ".php")) {
                require ROOT_PATH . "/app/var/languages/" . $locale . ".php";
            } elseif (file_exists(ROOT_PATH . "/app/var/languages/en.php")) {
                // fallback to default
                require ROOT_PATH . "/app/var/languages/en.php";
            }

            $translate = new \Phalcon\Translate\Adapter\NativeArray(array(
                "content" => $messages
            ));
        } else {
            $translate = new Translation_Db(array(
                'db' => $di->get('db'),
                'locale' => $locale,
            ));
        }

        $this->_di->set('trans', $translate);
    }
}