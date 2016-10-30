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

namespace Core\Command;

use Core\Model\SettingsModel;
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
        $assetsManager = new Manager($this->getDI(), false);
        $assetsManager->installAssets();

        print ConsoleUtils::success('Assets successfully installed.') . PHP_EOL;
    }
}