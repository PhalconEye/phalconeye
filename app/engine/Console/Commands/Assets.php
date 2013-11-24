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

namespace Engine\Console\Commands;

use Engine\Asset\Manager,
    Engine\Console\ConsoleUtil,
    Engine\Console\AbstractCommand,
    Engine\Console\CommandInterface,
    Engine\Generator\Migrations;

use Phalcon\DI;

/**
 * Assets command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Assets extends AbstractCommand implements CommandInterface
{
    /**
     * Executes the command.
     *
     * @param DI $di Dependency injection.
     *
     * @return void|bool
     */
    public function run($di)
    {
        $action = $this->getOption(array('action', 1));
        if ($action == 'install') {
            $assetsManager = new Manager($di, false);
            $assetsManager->installAssets();

            print ConsoleUtil::success('Assets successfully installed.') . PHP_EOL;
        }
    }

    /**
     * Returns the command identifier.
     *
     * @return string
     */
    public function getCommands()
    {
        return array('assets');
    }

    /**
     * Prints the help for current command.
     *
     * @return void
     */
    public function getHelp()
    {
        print ConsoleUtil::headLine('Help:');
        print ConsoleUtil::textLine('Assets management');

        print ConsoleUtil::commandLine('assets install', 'Install assets from all modules');
        print PHP_EOL;
    }

    /**
     * Returns number of required parameters for this command.
     *
     * @return int
     */
    public function getRequiredParams()
    {
        return 1;
    }
}