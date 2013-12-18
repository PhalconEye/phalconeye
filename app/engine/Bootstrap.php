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

use Engine\Plugin\CacheAnnotation;
use Engine\Plugin\NotFound;
use Phalcon\Config as PhalconConfig;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;

/**
 * Bootstrap class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class Bootstrap implements BootstrapInterface
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
    }

    /**
     * Module name.
     *
     * @var string
     */
    protected $_moduleName = "";

    /**
     * Configuration.
     *
     * @var PhalconConfig
     */
    protected $_config;

    /**
     * Create Bootstrap.
     *
     * @param DiInterface $di Dependency injection.
     */
    public function __construct($di = null)
    {
        $this->__DIConstruct($di);
        $this->_config = $this->getDI()->get('config');
    }

    /**
     * Destroy bootstrap.
     */
    public function __destruct()
    {
        if ($this->_config->application->debug && $this->_config->installed) {
            $defaultModuleBootstrap = ucfirst(Application::$defaultModule) . '\Bootstrap';
            $defaultModuleBootstrap::handleProfiler($this->getDI(), $this->_config);
        }
    }

    /**
     * Register specific autoloaders.
     *
     * @return void
     */
    public function registerAutoloaders()
    {

    }

    /**
     * Register the services.
     *
     * @param DI $di Dependency injection.
     *
     * @throws Exception
     * @return void
     */
    public function registerServices($di)
    {
        if (empty($this->_moduleName)) {
            $class = new \ReflectionClass($this);
            throw new Exception('Bootstrap has no module name: ' . $class->getFileName());
        }

        $moduleDirectory = $this->getModuleDirectory();
        $config = $this->_config;

        // Create an event manager.
        $eventsManager = new EventsManager($config);

        /*************************************************/
        //  Initialize view
        /*************************************************/
        $di->set(
            'view',
            function () use ($di, $moduleDirectory, $eventsManager, $config) {

                $view = new View();
                $view->setViewsDir($moduleDirectory . '/View/');

                $view->registerEngines(
                    [
                        ".volt" =>
                            function ($view, $di) use ($config) {
                                $volt = new Volt($view, $di);
                                $volt->setOptions(
                                    [
                                        "compiledPath" => $config->application->view->compiledPath,
                                        "compiledExtension" => $config->application->view->compiledExtension,
                                        'compiledSeparator' => $config->application->view->compiledSeparator,
                                        'compileAlways' => $config->application->view->compileAlways
                                    ]
                                );

                                $compiler = $volt->getCompiler();

                                // Register helper.
                                $compiler->addFunction(
                                    'helper',
                                    function ($resolvedArgs) use ($di) {
                                        return '(new \Engine\Helper(' . $resolvedArgs . '))';
                                    }
                                );

                                // Register translation filter.
                                $compiler->addFilter(
                                    'trans',
                                    function ($resolvedArgs) {
                                        return '$this->trans->query(' . $resolvedArgs . ')';
                                    }
                                );

                                $compiler->addFilter(
                                    'dump',
                                    function ($resolvedArgs) {
                                        return 'var_dump(' . $resolvedArgs . ')';
                                    }
                                );

                                return $volt;
                            }
                    ]
                );

                // Attach a listener for type "view".
                if (!$config->application->debug) {
                    $eventsManager->attach(
                        "view",
                        function ($event, $view) use ($di) {
                            if ($event->getType() == 'notFoundView') {
                                $di->get('logger')->error('View not found - "' . $view->getActiveRenderPath() . '"');
                            }
                        }
                    );

                    $view->setEventsManager($eventsManager);
                } elseif ($config->application->profiler) {
                    $eventsManager->attach(
                        "view",
                        function ($event, $view) use ($di) {
                            if ($di->has('profiler')) {
                                if ($event->getType() == 'beforeRender') {
                                    $di->get('profiler')->start();
                                }
                                if ($event->getType() == 'afterRender') {
                                    $di->get('profiler')->stop($view->getActiveRenderPath(), 'view');
                                }
                            }
                            if ($event->getType() == 'notFoundView') {
                                $di->get('logger')->error('View not found - "' . $view->getActiveRenderPath() . '"');
                            }
                        }
                    );
                    $view->setEventsManager($eventsManager);
                }

                return $view;
            }
        );

        /*************************************************/
        //  Initialize dispatcher
        /*************************************************/
        if (!$config->application->debug) {
            $eventsManager->attach("dispatch:beforeException", new NotFound());
            $eventsManager->attach('dispatch:beforeExecuteRoute', new CacheAnnotation());
        }

        /**
         * Listening to events in the dispatcher using the
         * Acl plugin
         */
        if ($di->get('config')->installed) {
            $eventsManager->attach('dispatch', $di->get(Application::$defaultModule)->acl());
        }

        // Create dispatcher
        $dispatcher = new Dispatcher();
        $dispatcher->setEventsManager($eventsManager);
        $di->set('dispatcher', $dispatcher);

    }

    /**
     * Get current module name.
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * Get current module directory.
     *
     * @return string
     */
    public function getModuleDirectory()
    {
        return $this->_config->application->modulesDir . $this->_moduleName;
    }
}