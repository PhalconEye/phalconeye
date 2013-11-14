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

use Phalcon\CLI\Console as PhConsole,
    Phalcon\DI\FactoryDefault\CLI as PhCliDi,
    Phalcon\Config as PhConfig,
    Phalcon\Events\Manager as PhEventsManager,
    Phalcon\Logger\Adapter\File as PhLogFile,
    Phalcon\Logger\Formatter\Line as PhLogFormatterLine,
    Phalcon\Loader as PhLoader,
    Phalcon\Mvc\Model\Manager as PhModelManager,
    Phalcon\Mvc\Model\MetaData\Memory as PhMetaData;

use Engine\Model\AnnotationsInitializer as PeAnnotationsInitializer,
    Engine\Model\AnnotationsMetaDataInitializer as PeAnnotationsMetaDataInitializer,
    Engine\Console\CommandsListener,
    Engine\Console\Color,
    Engine\Console\Command as PeCommand;


class Cli extends PhConsole
{
    // System config location.
    const SYSTEM_CONFIG_PATH = '/app/config/engine.php';

    /**
     * @var Config
     */
    private $_config;

    /**
     * Loaders.
     *
     * @var array
     */
    private $_loaders = array(
        'logger',
        'loader',
        'database'
    );

    /**
     * Defined engine commands.
     *
     * @var PeCommand[]
     */
    private $_commands = array();

    public function __construct()
    {
        // Create default di.
        $di = new PhCliDi();

        // Get config.
        $this->_config = include_once(ROOT_PATH . self::SYSTEM_CONFIG_PATH);

        // Store config in the Di container.
        $di->setShared('config', $this->_config);

        parent::__construct($di);
    }

    /**
     * Run application.
     */
    public function run()
    {
        // Set application event manager
        $eventsManager = new PhEventsManager();

        // Init services and engine system
        foreach ($this->_loaders as $service) {
            $this->{'init' . $service}($this->getDI(), $this->_config, $eventsManager);
        }

        // Init commands.
        $this->_commands[] = new \Engine\Console\Commands\Migration();

        $eventsManager->attach('command', new CommandsListener());
        $this->setEventsManager($eventsManager);
    }

    /**
     * Handle all data and output result.
     *
     * @throws Exception
     * @return mixed
     */
    public function getOutput()
    {
        $vendor = sprintf('PhalconEye Commands Manager');
        print PHP_EOL . Color::colorize($vendor, Color::FG_GREEN, Color::AT_BOLD) . PHP_EOL . PHP_EOL;

        if (!isset($_SERVER['argv'][1])) {
            $this->printAvailableCommands();
            die();
        }

        $input = $_SERVER['argv'][1];

        // Try to dispatch the command
        foreach ($this->_commands as $command) {
            $providedCommands = $command->getCommands();
            if (in_array($input, $providedCommands)) {
                $command->setConfig($this->_config);
                return $this->dispatch($command);
            }
        }

        //Check for alternatives
        $available = array();
        foreach ($this->_commands as $command) {
            $providedCommands = $command->getCommands();
            foreach ($providedCommands as $command) {
                $soundex = soundex($command);
                if (!isset($available[$soundex])) {
                    $available[$soundex] = array();
                }
                $available[$soundex][] = $command;
            }
        }

        // Show exception with/without alternatives
        $soundex = soundex($input);
        if (isset($available[$soundex])) {
            print Color::colorize('Command "' . $input . '" not found. Did you mean: ' . join(' or ', $available[$soundex]) . '?', Color::FG_RED, Color::AT_BOLD) . PHP_EOL . PHP_EOL;
            $this->printAvailableCommands();
        } else {
            print Color::colorize('Command "' . $input . '" not found.', Color::FG_RED, Color::AT_BOLD) . PHP_EOL . PHP_EOL;
            $this->printAvailableCommands();
        }
    }

    /**
     * Output available commands.
     */
    public function printAvailableCommands()
    {
        print Color::colorize('Available commands:', COLOR::FG_BROWN) . PHP_EOL;
        foreach ($this->_commands as $commands) {
            $providedCommands = $commands->getCommands();
            print '  ' . Color::colorize($providedCommands[0], Color::FG_GREEN);
            unset($providedCommands[0]);
            if (count($providedCommands)) {
                print ' (alias of: ' . Color::colorize(join(', ', $providedCommands)) . ')';
            }
            print PHP_EOL;
        }
        print PHP_EOL;
    }

    /**
     * Dispatch commands.
     *
     * @param PeCommand $command
     *
     * @return bool
     */
    public function dispatch(PeCommand $command)
    {
        //If beforeCommand fails abort
        if ($this->_eventsManager->fire('command:beforeCommand', $command) === false) {
            return false;
        }

        //If run the commands fails abort too
        if ($command->run($command->getParameters()) === false) {
            return false;
        }

        $this->_eventsManager->fire('command:afterCommand', $command);
    }

    /**
     * Initializes the logger.
     *
     * @param PhCliDi       $di            Current DI.
     * @param PhConfig      $config        Application config.
     * @param EventsManager $eventsManager Application events manager.
     *
     * @return void
     */
    protected function initLogger($di, $config, $eventsManager)
    {
        if ($config->application->logger->enabled) {
            $di->set('logger', function () use ($config) {
                $logger = new PhLogFile($config->application->logger->path . "cli.log");
                $formatter = new PhLogFormatterLine($config->application->logger->format);
                $logger->setFormatter($formatter);
                return $logger;
            });
        }
    }

    /**
     * Initializes the loader.
     *
     * @param PhCliDi       $di            Current DI.
     * @param PhConfig      $config        Application config.
     * @param EventsManager $eventsManager Application events manager.
     *
     * @return void
     */
    protected function initLoader($di, $config, $eventsManager)
    {
        $engineNamespaces = array();
        $engineNamespaces['Engine'] = $config->application->engineDir;
        $engineNamespaces['Plugin'] = $config->application->pluginsDir;
        $engineNamespaces['Widget'] = $config->application->widgetsDir;
        $engineNamespaces['Library'] = $config->application->librariesDir;

        $loader = new PhLoader();
        $loader->registerNamespaces($engineNamespaces);

        if ($config->application->debug) {
            $eventsManager->attach('loader', function ($event, $loader, $className) use ($di) {
                if ($event->getType() == 'afterCheckClass') {
                    $di->get('logger')->error("Can't load class '" . $className . "'");
                }
            });
            $loader->setEventsManager($eventsManager);
        }

        $loader->register();

        $modulesNamespaces = array();
        // add default module and engine modules
        $modules = array(
            Application::$defaultModule => true,
            'user' => true,
        );

        $enabledModules = $this->_config->get('modules');
        if (!$enabledModules) {
            $enabledModules = array();
        } else {
            $enabledModules = $enabledModules->toArray();
        }

        $modules = array_merge($modules, $enabledModules);
        foreach ($modules as $module => $enabled) {
            if (!$enabled) {
                continue;
            }
            $modulesNamespaces[ucfirst($module)] = $this->_config->application->modulesDir . ucfirst($module);
        }
        $loader->registerNamespaces($modulesNamespaces, true);
        $loader->register();

        $di->set('modules', $modules);
        $di->set('loader', $loader);
    }

    /**
     * Initializes the database.
     *
     * @param PhCliDi       $di            Current DI.
     * @param PhConfig      $config        Application config.
     * @param EventsManager $eventsManager Application events manager.
     *
     * @return void
     */
    protected function initDatabase($di, $config, $eventsManager)
    {
        $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        $connection = new $adapter(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
        ));
        $di->set('db', $connection);

        /**
         * Manager.
         */
        $di->set('modelsManager', function () use ($config, $eventsManager) {
            $modelsManager = new PhModelManager();
            $modelsManager->setEventsManager($eventsManager);

            //Attach a listener to models-manager
            $eventsManager->attach('modelsManager', new PeAnnotationsInitializer());

            return $modelsManager;
        }, true);

        /**
         * Metadata.
         */
        $di->set('modelsMetadata', function () use ($config) {
            $metaData = new PhMetaData();
            $metaData->setStrategy(new PeAnnotationsMetaDataInitializer());
            return $metaData;
        }, true);

    }
}