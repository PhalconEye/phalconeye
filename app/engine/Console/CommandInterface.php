<?php
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
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
use Phalcon\DI;

/**
 * Command interface.
 *
 * @category  PhalconEye
 * @package   Engine\Console
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface CommandInterface
{
    /**
     * Executes the command.
     *
     * @param DI $di Dependency injection.
     *
     * @return void|bool
     */
    public function run($di);

    /**
     * Returns the command identifier
     *
     * @return string
     */
    public function getCommands();

    /**
     * Get possible parameters.
     *
     * @return array
     */
    public function getPossibleParams();

    /**
     * Prints help on the usage of the command.
     *
     */
    public function getHelp();

    /**
     * Parse the parameters passed to the script.
     *
     * @param array $parameters    Parameters to parse.
     * @param array $possibleAlias Possible command alias.
     *
     * @throws CommandsException
     * @return array
     */
    public function parseParameters($parameters = array(), $possibleAlias = array());

    /**
     * Return required parameters
     *
     * @return mixed
     */
    public function getRequiredParams();
}