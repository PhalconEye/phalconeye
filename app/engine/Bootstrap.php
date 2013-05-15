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

abstract class Bootstrap implements BootstrapInterface
{

    /**
     * @var string
     */
    protected $_moduleName = "";

    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var \Phalcon\DiInterface
     */
    protected $_di;

    public function __construct()
    {
        $this->_di = \Phalcon\DI::getDefault();
        $this->_config = $this->_di->get('config');
    }

    public static function dependencyInjection(\Phalcon\DiInterface $di){

    }

    public function registerAutoloaders()
    {

    }

    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    public function registerServices($di)
    {
        if (empty($this->_moduleName)) {
            $class = new \ReflectionClass($this);
            throw new \Engine\Exception('Bootstrap has no module name: ' . $class->getFileName());
        }

        $moduleDirectory = $this->getModuleDirectory();
        $config = $this->_config;


        /*************************************************/
        //  Initialize view
        /*************************************************/
        $di->set('view', function () use ($moduleDirectory) {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir($moduleDirectory . '/View/');

            $view->registerEngines(array(
                ".volt" => function ($view, \Phalcon\DiInterface $di) {
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                    $volt->setOptions(array(
                        "compiledPath" => $di->get('config')->application->view->compiledPath,
                        "compiledExtension" => $di->get('config')->application->view->compiledExtension,
                        'compiledSeparator' => '_',
                        'compileAlways' => $di->get('config')->application->debug
                    ));

                    $compiler = $volt->getCompiler();

                    //register helper
                    $compiler->addFunction('helper', function ($resolvedArgs) use ($di) {
                        return '(new \Engine\Helper(' . $resolvedArgs . '))';
                    });

                    // register translation filter
                    $compiler->addFilter('trans', function ($resolvedArgs) {
                        return '\Phalcon\DI::getDefault()->get("trans")->query(' . $resolvedArgs . ')';
                    });

                    return $volt;
                }
            ));

            //Create an event manager
            $eventsManager = new EventsManager();

            //Attach a listener for type "view"
            $eventsManager->attach("view", function ($event, $view) {
                if ($event->getType() == 'notFoundView') {
                    \Phalcon\DI::getDefault()->get('logger')->error('View not found - "' . $view->getActiveRenderPath().'"');
                }
            });
            $view->setEventsManager($eventsManager);

            return $view;
        });


        /*************************************************/
        //  Initialize dispatcher
        /*************************************************/
        $eventsManager = $di->getShared('eventsManager');
        if (!$config->application->debug) {
            $eventsManager->attach(
                "dispatch:beforeException", function ($event, $dispatcher, $exception) use($config) {
                //The controller exists but the action not
                if ($event->getType() == 'beforeNotFoundAction') {
                    $dispatcher->forward(array(
                        'module' => Application::$defaultModule,
                        'namespace' => ucfirst(Application::$defaultModule) . '\Controller',
                        'controller' => 'Error',
                        'action' => 'show404'
                    ));
                    return false;
                }

                //Alternative way, controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                'module' => Application::$defaultModule,
                                'namespace' => ucfirst(Application::$defaultModule) . '\Controller',
                                'controller' => 'Error',
                                'action' => 'show404'
                            ));
                            return false;
                    }
                }
            });

            $eventsManager->attach('dispatch', new \Engine\Plugin\CacheAnnotation());
        }


        /**
         * Listening to events in the dispatcher using the
         * Acl plugin
         */
        $eventsManager->attach('dispatch', $di->get(Application::$defaultModule)->acl());

        // Create dispatcher
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $dispatcher->setEventsManager($eventsManager);
        $di->set('dispatcher', $dispatcher);

    }


    public function getModuleName()
    {
        return $this->_moduleName;
    }

    public function getModuleDirectory()
    {
        return $this->_config->application->modulesDir . $this->_moduleName;
    }
}