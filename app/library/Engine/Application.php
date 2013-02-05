<?php

use \Phalcon\Config\Adapter\Ini as PhConfig;
use \Phalcon\Loader as PhLoader;
use \Phalcon\Flash\Direct as PhFlash;
use \Phalcon\Logger\Adapter\File as PhLogger;
use \Phalcon\Db\Adapter\Pdo\Mysql as PhMysql;
use \Phalcon\Session\Adapter\Files as PhSession;
use \Phalcon\Cache\Frontend as PhCacheFront;
use \Phalcon\Cache\Backend as PhCacheBack;
use \Phalcon\Mvc\Application as PhApplication;
use \Phalcon\Mvc\Dispatcher as PhDispatcher;
use \Phalcon\Mvc\Router as PhRouter;
use \Phalcon\Mvc\Url as PhUrl;
use \Phalcon\Mvc\View as PhView;
use \Phalcon\Mvc\View\Engine\Volt as PhVolt;
use \Phalcon\Mvc\Model\Metadata\Memory as PhMetadataMemory;
use \Phalcon\Events\Manager as PhEventsManager;
use \Phalcon\Exception as PhException;

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
            'loader',
            'dispatcher',
            'environment',
            'url',
            'cache',
            'router',
            'view',
            'logger',
            'database',
            'session',
            'flash'
        );
        foreach ($loaders as $service) {
            $this->{'init' . $service}($this->_config);
        }

        $application = new PhApplication();
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
        $loader = new PhLoader();

        // Register the namespaces
        $loader
            ->registerDirs(array(
            $config->application->engineDir,
            $config->application->controllersDir,
            $config->application->modelsDir,
            $config->application->miscDir,
        ))
            ->register();

    }

    protected function initDispatcher($config)
    {
        $di = $this->_di;
        $di->set('dispatcher', function () use ($di, $config) {
            $evtManager = $di->getShared('eventsManager');
            if (!$config->application->debug) {


                $evtManager->attach(
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
            }

            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($evtManager);
            return $dispatcher;
        });
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

        $translate = new Translation_GetText(array(
            'locale' => 'ru_RU',
            'file' => 'messages',
            'directory' => ROOT_PATH . '/app/var/languages'
        ));

        $this->_di->set('trans', $translate);
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
            $url = new PhUrl();
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
        $di = $this->_di;

        $this->_di->set('router', function () use ($config, $di) {
            $router = new PhRouter();
            $router->add('/:controller/:action', array(
                'controller' => 1,
                'action' => 2,
            ));

            foreach ($config->router as $path => $routerSettings) {
                $router->add($path, $routerSettings->toArray());
            }

            return $router;
        });
    }

    /**
     * Initializes the view
     *
     * @param \stdClass $config
     */
    protected function initView($config)
    {
        $this->_di->set('view', function () use ($config) {
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
                    $compiler->addFunction('helper', function($resolvedArgs, $exprArgs) {
                        $name = $exprArgs[0]['expr']['value'];

                        $resolvedArgs = explode(', ', $resolvedArgs);
                        unset($resolvedArgs[0]);
                        $resolvedArgs = implode(', ', $resolvedArgs);
                        return 'Helper_'.ucfirst($name).'::_('.$resolvedArgs.')';
                    });

                    // register main filter
                    $compiler->addFilter('trans', function($resolvedArgs) {
                        return 'Helper_Translate::_('.$resolvedArgs.')';
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
                $logger = new PhLogger($config->application->logger->path . "main.log");
                $logger->setFormat($config->application->logger->format);
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
            $connection = new PhMysql(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->name,
            ));

            if ($config->application->debug || true){
                $eventsManager = new Phalcon\Events\Manager();

                $logger = new \Phalcon\Logger\Adapter\File($config->application->logger->path . "db.log");

                //Listen all the database events
                $eventsManager->attach('db', function($event, $connection) use ($logger) {
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
            $session = new PhSession();
            $session->start();
            return $session;
        }, true);
    }

    /**
     * Initializes the cache
     *
     * @param \stdClass $config
     */
    protected function initCache($config)
    {
        $this->_di->set('cacheOutput', function () use ($config) {
            // Get the parameters
            $lifetime = $config->application->cache->lifetime;
            $cacheDir = $config->application->cache->cacheDir;
            $frontEndOptions = array('lifetime' => ($config->application->debug ? 0 : $lifetime));
            $backEndOptions = array('cacheDir' => $cacheDir);

            $frontCache = new PhCacheFront\Output($frontEndOptions);
            $cache = new PhCacheBack\File($frontCache, $backEndOptions);

            return $cache;
        });

        $this->_di->set('cacheData', function () use ($config) {
            // Get the parameters
            $lifetime = $config->application->cache->lifetime;
            $cacheDir = $config->application->cache->cacheDir;
            $frontEndOptions = array('lifetime' => ($config->application->debug ? 0 : $lifetime));
            $backEndOptions = array('cacheDir' => $cacheDir);

            $frontCache = new PhCacheFront\Data($frontEndOptions);
            $cache = new PhCacheBack\File($frontCache, $backEndOptions);

            return $cache;
        });

        $this->_di->set('modelsCache', function () use ($config) {
            // Get the parameters
            $lifetime = $config->application->cache->lifetime;
            $cacheDir = $config->application->cache->cacheDir;
            $frontEndOptions = array('lifetime' => ($config->application->debug ? 0 : $lifetime));
            $backEndOptions = array('cacheDir' => $cacheDir);

            $frontCache = new PhCacheFront\Data($frontEndOptions);
            $cache = new PhCacheBack\File($frontCache, $backEndOptions);

            return $cache;
        });
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
    }


}