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
*/

namespace Engine\Console;

use Phalcon\DI;

/**
 * Phalcon\Commands\CommandInterface
 *
 * This interface must be implemented by all commands
 */
interface CommandsInterface
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
     * Prints help on the usage of the command
     *
     */
    public function getHelp();

}