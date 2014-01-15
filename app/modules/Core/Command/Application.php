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

namespace Core\Command;

use Core\Model\Package;
use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Console\ConsoleUtil;
use Engine\Exception;
use Engine\Package\Manager;
use Phalcon\DI;

/**
 * Application command.
 *
 * @category  PhalconEye
 * @package   Core\Commands
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Application extends AbstractCommand implements CommandInterface
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
        $action = $this->getOption(['action', 1]);
        if ($action == 'sync') {
            try {
                $manager = new Manager(Package::find(), $di);
                $manager->generateMetadata();
                print ConsoleUtil::success('Application successfully synchronized.') . PHP_EOL;
            } catch (Exception $e) {
                print ConsoleUtil::error($e->getMessage()) . PHP_EOL;
            }
        }
    }

    /**
     * Returns the command identifier.
     *
     * @return string
     */
    public function getCommands()
    {
        return ['application', 'app'];
    }

    /**
     * Prints the help for current command.
     *
     * @return void
     */
    public function getHelp()
    {
        print ConsoleUtil::headLine('Help:');
        print ConsoleUtil::textLine('Application commands');

        print ConsoleUtil::commandLine(
            'application sync',
            'Generate application metadata files and check packages in database.'
        );
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