<?php

/**
 * \Ph\Bootstrap
 * Bootstrap.php
 *
 * Bootstraps the application
 *
 * @author      Nikos Dimopoulos <nikos@niden.net>
 * @since       2012-11-01
 * @category    Library
 *
 */

namespace Ph;

use \Phalcon\Config\Adapter\Ini as PhConfig;
use \Phalcon\Loader as PhLoader;
use \Phalcon\Flash\Direct as PhFlash;
use \Phalcon\Logger\Adapter\File as PhLogger;
use \Phalcon\Db\Adapter\Pdo\Mysql as PhMysql;
use \Phalcon\Session\Adapter\Files as PhSession;
use \Phalcon\Cache\Frontend\Output as PhCacheFront;
use \Phalcon\Cache\Backend\File as PhCacheBack;
use \Phalcon\Mvc\Application as PhApplication;
use \Phalcon\Mvc\Dispatcher as PhDispatcher;
use \Phalcon\Mvc\Router as PhRouter;
use \Phalcon\Mvc\Url as PhUrl;
use \Phalcon\Mvc\View as PhView;
use \Phalcon\Mvc\View\Engine\Volt as PhVolt;
use \Phalcon\Mvc\Model\Metadata\Memory as PhMetadataMemory;
use \Phalcon\Events\Manager as PhEventsManager;
use \Phalcon\Exception as PhException;

class Bootstrap
{
	private $_di;

	private $_config;

	/**
	 * Constructor
	 *
	 * @param $di
	 */
	public function __construct($di)
	{

		$configFile = ROOT_PATH . '/app/config/config.ini';

		// Create the new object
		$config = new PhConfig($configFile);

		// Store it in the Di container
		$di->setShared('config', $config);

		$this->_di = $di;
		$this->_config = $config;
	}

	/**
	 * Runs the application performing all initializations
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function run($options)
	{

		$config = $this->_config;

		$loaders = array(
			'loader',
			'environment',
			'url',
			'dispatcher',
			'router',
			'view',
			'logger',
			'database',
			'session',
			'cache',
		);
		foreach ($loaders as $service){
			$this->{'init' . $service}($config, $options);
		}

		$application = new PhApplication();
		$application->setDI($this->_di);

		return $application->handle()->getContent();
	}

	// Protected functions

	/**
	 * Initializes the loader
	 *
	 * @param array $options
	 */
	protected function initLoader($config)
	{
		// Creates the autoloader
		$loader = new PhLoader();

		$loader->registerDirs(
			array(
				ROOT_PATH . $config->app->path->controllers,
				ROOT_PATH . $config->app->path->models,
				ROOT_PATH . $config->app->path->library,
			)
		);

		// Register the namespace
		$loader->registerNamespaces(
			array("Ph" => $config->app->path->library)
		);

		$loader->register();
	}

	/**
	 * Initializes the environment
	 *
	 * @param Phalcon\Config $config
	 */
	protected function initEnvironment($config)
	{
		set_error_handler(array('\Ph\Error', 'normal'));
		set_exception_handler(array('\Ph\Error', 'exception'));
	}

	/**
	 * Initializes the baseUrl
	 *
	 * @param Phalcon\Config $config
	 */
	protected function initUrl($config)
	{
		/**
		 * The URL component is used to generate all kind of urls in the
		 * application
		 */
		$this->_di->set('url', function() use ($config) {
			$url = new PhUrl();
			$url->setBaseUri($config->app->baseUri);
			return $url;
		});
	}

	/**
	 * Initializes the dispatcher
	 *
	 * @param Phalcon\Config $config
	 */
	protected function initDispatcher($config)
	{
		$di = $this->_di;

		$di->set('dispatcher', function() use ($di) {

			$evManager = $di->getShared('eventsManager');

			$evManager->attach(
				"dispatch:beforeException", function($event, $dispatcher, $exception){
					switch ($exception->getCode()) {
						case PhDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
						case PhDispatcher::EXCEPTION_ACTION_NOT_FOUND:
							$dispatcher->forward(array(
								'controller' => 'index',
								'action' => 'show404'
							));
							return false;
					}
			});
			$dispatcher = new PhDispatcher();
			$dispatcher->setEventsManager($evManager);

			return $dispatcher;
		});
	}

	public function initRouter()
	{
		$this->_di->set('router', function() {

			$router = new PhRouter();

			$router->add("/documentation/([a-zA-Z0-9_]+)",
				array(
					"controller" => "documentation",
					"action" => "redirect",
					"name" => 1,
				)
			);

			$router->add("/documentation/index", array(
				"controller" => "documentation",
				"action" => "index"
			));

			$router->add("/documentation", array(
				"controller" => "documentation",
				"action" => "index"
			));

			return $router;
		});
	}

	/**
	 * Initializes the view
	 *
	 * @param Phalcon\Config $config
	 */
	protected function initView($config)
	{
		$di     = $this->_di;

		/**
		 * Setup the view service
		 */
		$this->_di->set('view', function() use ($config, $di) {
			$view = new PhView();
			$view->setViewsDir(ROOT_PATH . $config->app->path->views);
			$view->registerEngines(array(
				'.volt' => function($view, $di) use ($config) {
					$volt = new PhVolt($view, $di);
					$volt->setOptions(
						array(
							'compiledPath'      => ROOT_PATH . $config->app->volt->path,
							'compiledExtension' => $config->app->volt->extension,
							'compiledSeparator' => $config->app->volt->separator,
							'stat'              => (bool) $config->app->volt->stat,
						)
					);
					return $volt;
				}));
				return $view;
			}
		);
	}
	/**
	 * Initializes the logger
	 *
	 * @param Phalcon\Config $config
	 */
	protected function initLogger($config)
	{
		$this->_di->set('logger', function() use ($config) {
			$logger = new PhLogger(ROOT_PATH . $config->app->logger->file);
			$logger->setFormat($config->app->logger->format);
			return $logger;
		});
	}

	/**
	 * Initializes the database and netadata adapter
	 *
	 * @param array $options
	 */
	protected function initDatabase($config)
	{

		$this->_di->set('db', function() use ($config) {

			$connection = new PhMysql(array(
				"host"     => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname"   => $config->database->name,
			));

			return $connection;
		});

		/**
		 * If the configuration specify the use of metadata adapter use it or use memory otherwise
		 */
		$this->_di->set('modelsMetadata', function() use ($config) {
			if (isset($config->models->metadata)) {
				$metaDataConfig  = $config->models->metadata;
				$metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\'.$metaDataConfig->adapter;
				return new $metadataAdapter();
			} else {
				return new PhMetadataMemory();
			}
		}, true);
	}

	/**
	 * Initializes the session
	 *
	 * @param array $options
	 */
	protected function initSession()
	{
		$this->_di->set('session', function() {
			$session = new PhSession();
			$session->start();
			return $session;
		}, true);
	}

	/**
	 * Initializes the cache
	 *
	 * @param array $options
	 */
	protected function initCache($config)
	{
		$this->_di->set('viewCache', function() use ($config) {

			// Get the parameters
			$lifetime        = $config->app->cache->lifetime;
			$cacheDir        = $config->app->cache->cacheDir;
			$frontEndOptions = array('lifetime' => $lifetime);
			$backEndOptions  = array('cacheDir' => ROOT_PATH . $cacheDir);

			$frontCache = new PhCacheFront($frontEndOptions);
			$cache      = new PhCacheBack($frontCache, $backEndOptions);

			return $cache;
		});
	}

}
