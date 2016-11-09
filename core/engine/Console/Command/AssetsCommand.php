<?php
/*
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconeye.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
 +------------------------------------------------------------------------+
*/

namespace Engine\Console\Command;

use Engine\Asset\Manager;
use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Utils\ConsoleUtils;

/**
 * Assets command.
 *
 * @category  PhalconEye
 * @package   Core\Commands
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @CommandName(['assets'])
 * @CommandDescription('Assets management.')
 */
class AssetsCommand extends AbstractCommand implements CommandInterface
{
    /**
     * Install assets from modules.
     *
     * @return void
     */
    public function installAction()
    {
        $this->getAssets()->installAssets();
        print ConsoleUtils::successLine('Assets successfully installed.') . PHP_EOL;
    }

    /**
     * Clear assets folder.
     *
     * @param bool $refresh Install new assets after clear.
     *
     * @return void
     */
    public function clearAction($refresh = false)
    {
        $this->getAssets()->clear($refresh);
        print ConsoleUtils::successLine('Assets successfully cleared.') . PHP_EOL;
    }
}