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

namespace Engine;

use Phalcon\DI;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Application as PhalconApplication;
use Phalcon\Registry;

/**
 * Application class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Application extends PhalconApplication
{
    const
        /**
         * Default module.
         */
        SYSTEM_DEFAULT_MODULE = 'core';

    use ApplicationInitialization;

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
                'environment',
                'database',
                'cache',
                'engine'
            ],
            'session' => [
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
        /**
         * Create default DI.
         */
        $di = new DI\FactoryDefault();

        /**
         * Get config.
         */
        $this->_config = Config::factory();

        if (!$this->_config->installed) {
            define('CHECK_REQUIREMENTS', true);
            require_once(PUBLIC_PATH . '/requirements.php');
        }

        /**
         * Setup Registry.
         */
        $registry = new Registry();
        $registry->modules = array_merge(
            [self::SYSTEM_DEFAULT_MODULE, 'user'],
            $this->_config->modules->toArray()
        );

        $registry->directories = (object)[
            'engine' => ROOT_PATH . '/app/engine/',
            'modules' => ROOT_PATH . '/app/modules/',
            'plugins' => ROOT_PATH . '/app/plugins/',
            'widgets' => ROOT_PATH . '/app/widgets/',
            'libraries' => ROOT_PATH . '/app/libraries/'
        ];

        $di->set('registry', $registry);

        // Store config in the DI container.
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

        // Init base systems first.
        $this->_initLogger($di, $config);
        $this->_initLoader($di, $config, $eventsManager);

        $this->_attachEngineEvents($eventsManager, $config);

        // Init services and engine system.
        foreach ($this->_loaders[$mode] as $service) {
            $serviceName = ucfirst($service);
            $eventsManager->fire('init:before' . $serviceName, null);
            $result = $this->{'_init' . $serviceName}($di, $config, $eventsManager);
            $eventsManager->fire('init:after' . $serviceName, $result);
        }

        $di->setShared('eventsManager', $eventsManager);
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
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->handle()->getContent();
    }

    /**
     * Clear application cache.
     *
     * @param string $themeDirectory Theme directory.
     *
     * @return void
     */
    public function clearCache($themeDirectory = '')
    {
        $cacheOutput = $this->_dependencyInjector->get('cacheOutput');
        $cacheData = $this->_dependencyInjector->get('cacheData');
        $config = $this->_dependencyInjector->get('config');

        $cacheOutput->flush();
        $cacheData->flush();


        // Files deleter helper.
        $deleteFiles = function ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        };

        // Clear files cache.
        if (isset($config->application->cache->cacheDir)) {
            $deleteFiles(glob($config->application->cache->cacheDir . '*'));
        }

        // Clear view cache.
        $deleteFiles(glob($config->application->view->compiledPath . '*'));

        // Clear metadata cache.
        if ($config->application->metadata && $config->application->metadata->metaDataDir) {
            $deleteFiles(glob($config->application->metadata->metaDataDir . '*'));
        }

        // Clear annotations cache.
        if ($config->application->annotations && $config->application->annotations->annotationsDir) {
            $deleteFiles(glob($config->application->annotations->annotationsDir . '*'));
        }

        // Clear assets.
        $this->_dependencyInjector->getShared('assets')->clear(true, $themeDirectory);
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
        $events = $config->events->toArray();
        $cache = [];
        foreach ($events as $item) {
            list ($class, $event) = explode('=', $item);
            if (isset($cache[$class])) {
                $object = $cache[$class];
            } else {
                $object = new $class();
                $cache[$class] = $object;
            }
            $eventsManager->attach($event, $object);
        }
    }
}