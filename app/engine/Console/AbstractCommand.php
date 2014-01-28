<?php
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2013 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+

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

namespace Engine\Console;

use Engine\DependencyInjection;
use Phalcon\Config;
use Phalcon\Filter;

/**
 * Abstract command.
 *
 * @category  PhalconEye
 * @package   Engine\Console
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractCommand implements CommandInterface
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
    }

    /**
     * Parameters received by the script.
     *
     * @var string
     */
    protected $_parameters = [];

    /**
     * Possible prepared arguments.
     *
     * @var array
     */
    protected $_preparedArguments = [];

    /**
     * Engine config.
     *
     * @var null
     */
    protected $_config = null;

    /**
     * Final constructor.
     *
     */
    final public function __construct()
    {
        $this->__DIConstruct();
    }

    /**
     * Parse the parameters passed to the script.
     *
     * @param array $parameters    Parameters to parse.
     * @param array $possibleAlias Possible command alias.
     *
     * @throws CommandsException
     * @return array
     */
    public function parseParameters($parameters = [], $possibleAlias = [])
    {
        if (count($parameters) == 0) {
            $parameters = $this->getPossibleParams();
        }

        if (!is_array($parameters)) {
            throw new CommandsException("Cannot load possible parameters for script: " . get_class($this));
        }

        $arguments = [];
        foreach (array_keys($parameters) as $parameter) {
            if (strpos($parameter, "=") !== false) {
                $parameterParts = explode("=", $parameter);
                if (count($parameterParts) != 2) {
                    throw new CommandsException("Invalid definition for the parameter '$parameter'");
                }
                if (strlen($parameterParts[0]) == "") {
                    throw new CommandsException("Invalid definition for the parameter '" . $parameter . "'");
                }
                if (!in_array($parameterParts[1], ['s', 'i', 'l'])) {
                    throw new CommandsException("Incorrect data type on parameter '" . $parameter . "'");
                }
                $this->_preparedArguments[$parameterParts[0]] = true;
                $arguments[$parameterParts[0]] = [
                    'have-option' => true,
                    'option-required' => true,
                    'data-type' => $parameterParts[1]
                ];
            } else {
                if (preg_match('/([a-zA-Z0-9]+)/', $parameter)) {
                    $this->_preparedArguments[$parameter] = true;
                    $arguments[$parameter] = [
                        'have-option' => false,
                        'option-required' => false
                    ];
                } else {
                    throw new CommandsException("Invalid parameter '$parameter'");
                }
            }
        }

        return $this->_parseArguments($possibleAlias);
    }

    /**
     * Get possible parameters.
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [];
    }

    /**
     * Parse arguments from input.
     *
     * @param array $possibleAlias Possible command alias.
     *
     * @throws CommandsException
     * @return array
     */
    protected function _parseArguments($possibleAlias = [])
    {
        $param = '';
        $paramName = '';
        $receivedParams = [];
        $argumentsCount = count($_SERVER['argv']);

        for ($i = 1; $i < $argumentsCount; $i++) {
            $argv = $_SERVER['argv'][$i];
            if (preg_match('#^([\-]{1,2})([a-zA-Z0-9][a-zA-Z0-9\-]*)(=(.*)){0,1}$#', $argv, $matches)) {

                if (strlen($matches[1]) == 1) {
                    $param = substr($matches[2], 1);
                    $paramName = substr($matches[2], 0, 1);
                } else {
                    if (strlen($matches[2]) < 2) {
                        throw new CommandsException("Invalid script parameter '$argv'");
                    }
                    $paramName = $matches[2];
                }

                if (!isset($this->_preparedArguments[$paramName])) {
                    if (!isset($possibleAlias[$paramName])) {
                        throw new CommandsException("Unknown parameter '$paramName'");
                    } else {
                        $paramName = $possibleAlias[$paramName];
                    }
                }

                if (isset($arguments[$paramName])) {
                    if ($param != '') {
                        $receivedParams[$paramName] = $param;
                        $param = '';
                        $paramName = '';
                    }
                    if ($arguments[$paramName]['have-option'] == false) {
                        $receivedParams[$paramName] = true;
                    } else {
                        if (isset($matches[4])) {
                            $receivedParams[$paramName] = $matches[4];
                        }
                    }
                }

            } else {
                $param = $argv;
                if ($paramName != '') {
                    if (isset($arguments[$paramName])) {
                        if ($param == '') {
                            if ($arguments[$paramName]['have-option'] == true) {
                                throw new CommandsException("The parameter '$paramName' requires an option");
                            }
                        }
                    }
                    $receivedParams[$paramName] = $param;
                    $param = '';
                    $paramName = '';
                } else {
                    $receivedParams[$i - 1] = $param;
                    $param = '';
                }
            }
        }

        $this->_parameters = $receivedParams;

        return $receivedParams;
    }

    /**
     * Returns the value of an option received. If more parameters are taken as filters.
     *
     * @param string     $option       Option name.
     * @param null|array $filters      Filters array.
     * @param null|mixed $defaultValue Default value if option doesn't exists.
     *
     * @return mixed
     */
    public function getOption($option, $filters = null, $defaultValue = null)
    {
        if (is_array($option)) {
            foreach ($option as $optionItem) {
                if (isset($this->_parameters[$optionItem])) {
                    if ($filters !== null) {
                        $filter = new Filter();

                        return $filter->sanitize($this->_parameters[$optionItem], $filters);
                    }

                    return $this->_parameters[$optionItem];
                }
            }

            return $defaultValue;
        } else {
            if (isset($this->_parameters[$option])) {
                if ($filters !== null) {
                    $filter = new Filter();

                    return $filter->sanitize($this->_parameters[$option], $filters);
                }

                return $this->_parameters[$option];
            } else {
                return $defaultValue;
            }
        }
    }

    /**
     * Indicates whether the script was a particular option.
     *
     * @param string $option Option name.
     *
     * @return boolean
     */
    public function isReceivedOption($option)
    {
        return isset($this->_parameters[$option]);
    }

    /**
     * Prints the available options in the script.
     *
     * @param array $parameters Parameters array.
     *
     * @return void
     */
    public function printParameters($parameters)
    {
        $length = 0;
        foreach ($parameters as $parameter => $description) {
            if ($length == 0) {
                $length = strlen($parameter);
            }
            if (strlen($parameter) > $length) {
                $length = strlen($parameter);
            }
        }

        print ConsoleUtil::headLine('Options:');
        foreach ($parameters as $parameter => $description) {
            print ConsoleUtil::colorize(
                ' --' . $parameter . str_repeat(' ', $length - strlen($parameter)), ConsoleUtil::FG_GREEN
            );
            print ConsoleUtil::colorize("    " . $description) . PHP_EOL;
        }
    }

    /**
     * Returns the processed parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Return required parameters
     *
     * @return mixed
     */
    abstract public function getRequiredParams();

    /**
     * Prints help on the usage of the command
     */
    abstract public function getHelp();

    /**
     * Get engine config.
     *
     * @return null|Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set engine config.
     *
     * @param Config $config Application config.
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Filters a value.
     *
     * @param mixed $paramValue Value to filter.
     * @param mixed $filters    Filter to apply to value.
     *
     * @return mixed
     */
    protected function filter($paramValue, $filters)
    {
        $filter = new Filter();

        return $filter->sanitize($paramValue, $filters);
    }
}
