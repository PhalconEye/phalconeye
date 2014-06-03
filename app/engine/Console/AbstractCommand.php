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

namespace Engine\Console;

use Engine\Behaviour\DIBehaviour;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Filter;

/**
 * Abstract command.
 *
 * @category  PhalconEye
 * @package   Engine\Console
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractCommand implements CommandInterface
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * Resolved name of command.
     * It can be one of available aliases names.
     *
     * @var string
     */
    protected $_name;

    /**
     * Command name with aliases.
     *
     * @var array
     */
    protected $_commands = [];

    /**
     * Command description.
     *
     * @var string
     */
    protected $_description;

    /**
     * Available actions in this command.
     *
     * @var string
     */
    protected $_actions = [];

    /**
     * Parameters received by the script.
     *
     * @var string
     */
    protected $_parameters = [];

    /**
     * Final constructor.
     *
     * @param DiInterface $di Dependency injection container.
     */
    final public function __construct($di)
    {
        $this->__DIConstruct($di);
        $this->_setupCommandMetadata();
        $this->_setupActionsMetadata();
    }

    /**
     * Dispatch command. Find out action and exec it with parameters.
     *
     * @throws CommandsException
     * @return mixed
     */
    public function dispatch()
    {
        $noActions = empty($this->_actions);
        if (!$noActions && !$this->_parseParameters()) {
            return false;
        }

        // Resolve action naming.
        $actionName = $this->getParameter('action');
        $action = $actionName . 'Action';

        // Has actions, but action wasn't selected.
        if (!$noActions && !$actionName) {
            $this->getHelp();
            return false;
        }

        // Init command if required.
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }

        // Just has no actions.
        if ($noActions) {
            return true;
        }

        // Run command action.
        $actionParams = [];
        if (!empty($this->_actions[$actionName]['params'])) {
            foreach ($this->_actions[$actionName]['params'] as $key => $param) {
                if (!$this->hasParameter($param['name'])) {
                    $actionParams[$key] = $param['defaultValue'];
                    continue;
                }

                $actionParams[$key] = $this->getParameter($param['name']);
            }
        }
        return call_user_func_array([$this, $action], $actionParams);
    }


    /**
     * Get command name.
     *
     * @return string
     */
    public function getName()
    {
        if ($this->_name) {
            return $this->_name;
        }
        return array_shift($this->_commands);
    }

    /**
     * Get command actions available parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Returns the value of an parameter received.
     *
     * @param string     $name    Option name.
     * @param null|array $filter  Filters array.
     * @param null|mixed $default Default value if option doesn't exists.
     *
     * @return mixed
     */
    public function getParameter($name, $filter = null, $default = null)
    {
        if (!isset($this->_parameters[$name])) {
            return $default;
        }

        if ($filter) {
            $filterObject = new Filter();
            return $filterObject->sanitize($this->_parameters[$name], $filter);
        }

        return $this->_parameters[$name];
    }

    /**
     * Indicates whether the script was a particular option.
     *
     * @param string $name Option name.
     *
     * @return boolean
     */
    public function hasParameter($name)
    {
        return isset($this->_parameters[$name]);
    }

    /**
     * Get commands.
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->_commands;
    }

    /**
     * Get command description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get command actions.
     *
     * @return array
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * Prints the help for current command.
     *
     * @param string|null $action Action name.
     *
     * @return void
     */
    public function getHelp($action = null)
    {
        $commandName = $this->getName();
        if ($action) {
            if (empty($this->_actions[$action])) {
                print ConsoleUtil::warningLine("Action '$action' not found in this command.");
                return;
            }

            print ConsoleUtil::headLine('Help for "' . $commandName . ' ' . $action . '":');
            if (isset($this->_actions[$action]) && isset($this->_actions[$action]['description'])) {
                print ConsoleUtil::textLine($this->_actions[$action]['description']);
            } else {
                print ConsoleUtil::textLine($this->getDescription());
            }

            $this->printParameters($action);
            return;
        } else {
            print ConsoleUtil::headLine('Help:');
            print ConsoleUtil::textLine($this->getDescription());
        }

        foreach ($this->getActions() as $actionName => $metadata) {
            $description = isset($metadata['description']) ? $metadata['description'] : '';
            print ConsoleUtil::commandLine($commandName . ' ' . $actionName, $description);
        }
        print PHP_EOL;
    }

    /**
     * Prints the available options in the script.
     *
     * @param string $action Action name.
     *
     * @throws CommandsException
     * @return void
     */
    public function printParameters($action)
    {
        if (!$action) {
            return;
        }

        if (empty($this->_actions[$action])) {
            throw new CommandsException("Action '$action' not found in this command.");
        }

        if (empty($this->_actions[$action]['params'])) {
            return;
        }

        print ConsoleUtil::headLine('Available parameters:');
        foreach ($this->_actions[$action]['params'] as $parameter) {
            $cmd = ' --' . $parameter['name'];
            $type = '';

            if ($parameter['defaultValueType'] != 'boolean') {
                $cmd .= '=' . $parameter['defaultValueType'];
                $type = ' (' . $parameter['type'] . ')';
            }

            print '  ';
            print ConsoleUtil::colorize($cmd, ConsoleUtil::FG_CYAN);
            print ConsoleUtil::colorize($type, ConsoleUtil::FG_YELLOW);
            print ConsoleUtil::tab(ConsoleUtil::COMMENT_START_POSITION, strlen($cmd . $type) + 6);
            print ConsoleUtil::colorize($parameter['description'], ConsoleUtil::FG_BROWN);
            print PHP_EOL;
        }
    }

    /**
     * Parse the parameters passed to the script.
     *
     * @throws CommandsException
     * @return array
     */
    protected function _parseParameters()
    {
        $this->_parameters = [];
        $argumentsCount = count($_SERVER['argv']);
        $withoutValue = [];

        for ($i = 1; $i < $argumentsCount; $i++) {
            $argv = $_SERVER['argv'][$i];

            // Set initial data.
            if (in_array($argv, $this->_commands) && empty($this->_parameters)) {
                $this->_name = $argv;
                $this->_parameters = [
                    'command' => $argv,
                    'action' => false
                ];
                continue;
            }

            // Set action parameter.
            if (isset($this->_parameters['action']) && empty($this->_parameters['action'])) {
                // If action was provided empty - we need to show help info.
                if (empty($argv)) {
                    $this->getHelp();
                    return false;
                }

                // If wee entered unavailable method wee need to show an error and help info.
                if (!array_key_exists($argv, $this->_actions)) {
                    print ConsoleUtil::error(
                        sprintf(
                            'Action "%s" not found in command "%s"...',
                            $argv,
                            $this->_parameters['command']
                        )
                    );
                    $this->getHelp();
                    return false;
                }

                $this->_parameters['action'] = $argv;
                continue;
            }

            if (preg_match('#^([\-]{1,2})([a-zA-Z0-9][a-zA-Z0-9\-]*)(=(.*)){0,1}$#', $argv, $matches)) {
                if (empty($matches[2])) {
                    throw new CommandsException("Invalid script parameter '$argv'.");
                }

                if (empty($matches[4])) {
                    $this->_parameters[$matches[2]] = true;
                    $withoutValue[] = $matches[2];
                } else {
                    $this->_parameters[$matches[2]] = $matches[4];
                }
            } else {
                throw new CommandsException("Invalid script parameter '$argv'.");
            }
        }

        return $this->_checkParameters($withoutValue);
    }

    /**
     * Check passed parameters (required or no value).
     *
     * @param array $withoutValue Params without value.
     *
     * @return bool
     */
    private function _checkParameters($withoutValue)
    {
        $action = $this->_parameters['action'];
        if (!empty($this->_actions[$action]['params'])) {
            foreach ($this->_actions[$action]['params'] as $actionParams) {

                // Check required param.
                if (
                    $actionParams['defaultValueType'] == '<required>' &&
                    empty($this->_parameters[$actionParams['name']])
                ) {
                    print ConsoleUtil::error(
                        sprintf(
                            'Parameter "%s" is required!',
                            $actionParams['name']
                        )
                    );
                    $this->getHelp($action);
                    return false;
                }

                // Check required value of param.
                if (
                    $actionParams['defaultValueType'] != 'boolean' &&
                    in_array($actionParams['name'], $withoutValue)
                ) {
                    print ConsoleUtil::error(
                        sprintf(
                            'Parameter "%s" must have value!',
                            $actionParams['name']
                        )
                    );
                    $this->getHelp($action);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Setup command metadata.
     *
     * @throws CommandsException
     * @return void
     */
    private function _setupCommandMetadata()
    {
        $reflector = $this->getDI()->getAnnotations()->get($this);
        $annotations = $reflector->getClassAnnotations();
        if ($annotations) {
            foreach ($annotations as $annotation) {
                switch ($annotation->getName()) {
                    /**
                     * Initializes the model's source
                     */
                    case 'CommandName':
                        $arguments = $annotation->getArguments();
                        if (!isset($arguments[0]) || !is_array($arguments[0])) {
                            throw new CommandsException('Command name must be an array of available names.');
                        }
                        $this->_commands = $arguments[0];
                        break;
                    case 'CommandDescription':
                        $arguments = $annotation->getArguments();
                        if (!isset($arguments[0]) || !is_string($arguments[0])) {
                            throw new CommandsException('Command description must be a string.');
                        }
                        $this->_description = $arguments[0];
                        break;
                }
            }
        }
    }

    /**
     * Setup actions metadata.
     *
     * @return void
     */
    private function _setupActionsMetadata()
    {
        foreach (get_class_methods($this) as $method) {
            if (substr($method, -6) != 'Action') {
                continue;
            }

            // Method annotations.
            $reflection = new \ReflectionMethod(get_class($this), $method);

            // Method name.
            $method = str_replace('Action', '', $method);
            $this->_actions[$method] = [];

            // Get action description and params metadata.
            $paramsMetadata = $this->_getActionMetadata($reflection, $method);

            // Get action params.
            $this->_actions[$method]['params'] = [];
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $defaultValue = $parameter->getDefaultValue();
                $defaultValueType = $parameter->isDefaultValueAvailable() ?
                    gettype($defaultValue) : '<required>';

                $this->_actions[$method]['params'][] = [
                    'name' => $name,
                    'defaultValueType' => $defaultValueType,
                    'defaultValue' => $defaultValue,
                    'type' => (isset($paramsMetadata[$name]) ? $paramsMetadata[$name]['type'] : ''),
                    'description' => (isset($paramsMetadata[$name]) ? $paramsMetadata[$name]['description'] : '')
                ];
            }
        }
    }

    /**
     * Get action metadata.
     *
     * @param \ReflectionMethod $reflection Reflection object.
     * @param string            $method     Method name.
     *
     * @return array
     */
    private function _getActionMetadata($reflection, $method)
    {
        $docComment = $reflection->getDocComment();
        $paramsMetadata = [];
        $actionComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $actionComment = explode("\n", str_replace("\n\n", "\n", trim($actionComment, "\r\n")));

        if (!empty($actionComment)) {
            foreach ($actionComment as $comment) {
                if (strpos($comment, '@') === false) {
                    if (empty($this->_actions[$method]['description'])) {
                        $this->_actions[$method]['description'] = $comment;
                    } else {
                        $this->_actions[$method]['description'] .= $comment;
                    }
                } elseif (strpos($comment, '@param') !== false) {
                    $comment = preg_replace("/(?:\s)+/", " ", $comment, -1);
                    $paramOptions = explode(' ', $comment);
                    $description = '';

                    if (!empty($paramOptions[0]) && !empty($paramOptions[1]) && !empty($paramOptions[2])) {
                        $description = trim(
                            str_replace(
                                implode(' ', [$paramOptions[0], $paramOptions[1], $paramOptions[2]]),
                                '',
                                $comment
                            )
                        );
                    }

                    if (!empty($paramOptions[2])) {
                        $paramsMetadata[str_replace('$', '', $paramOptions[2])] = [
                            'type' => $paramOptions[1],
                            'description' => $description
                        ];
                    }
                }
            }
        }

        return $paramsMetadata;
    }
}
