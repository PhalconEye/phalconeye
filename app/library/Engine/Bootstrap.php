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

///////////////////////////////////////
/// UNUSED UNTIL MODELS IMPLEMENTATION
///////////////////////////////////////
abstract class Bootstrap
{
    protected $_moduleName = "";

    public function registerAutoloaders()
    {
        if (empty($this->_moduleName)){
            $class = new \ReflectionClass($this);
            throw new \Exception('Bootstrap has no module name: ' . $class->getFileName());
        }

        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            $this->_moduleName .'\Controllers' => ROOT_PATH . '/app/modules/' . $this->_moduleName . '/controllers/'
//            $this->_moduleName .'\Models' => ROOT_PATH . '/app/modules/' . $this->_moduleName . '/models/'
        ));

        $loader->register();
    }

    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    public function registerServices($di)
    {
        if (empty($this->_moduleName)){
            $class = new \ReflectionClass($this);
            throw new \Exception('Bootstrap has no module name: ' . $class->getFileName());
        }

        $bootstrap = $this;
        $di->set('view', function () use ($bootstrap) {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(ROOT_PATH . '/app/modules/' . $bootstrap->getModuleName() . '/views/');

            $view->registerEngines(array(
                ".volt" => function ($view, \Phalcon\DiInterface $di) {
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                    $volt->setOptions(array(
                        "compiledPath" => $di->get('config')->application->view->compiledPath,
                        "compiledExtension" => $di->get('config')->application->view->compiledExtension,
                        'compileAlways' => $di->get('config')->application->debug
                    ));
                    return $volt;
                }
            ));
            return $view;
        });

        $di->set('dispatcher', function() use ($bootstrap, $di) {
            $evtManager = $di->getShared('eventsManager');
            $evtManager->attach(
                "dispatch:beforeException", function ($event, $dispatcher, $exception) {
                switch ($exception->getCode()) {
                    case \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(array(
                            'module' => 'core',
                            'controller' => 'error',
                            'action' => 'show404'
                        ));
                        return false;
                }
            });

            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($evtManager);
            $dispatcher->setDefaultNamespace($bootstrap->getModuleName() . "\Controllers\\");
            return $dispatcher;
        });
    }


    public function getModuleName()
    {
        return $this->_moduleName;
    }
}