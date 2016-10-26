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

use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Utils\ConsoleUtils;
use Engine\Package\Manager;
use Engine\Package\PackageException;
use Phalcon\Validation\Validator\StringLength;

/**
 * Package command.
 *
 * @category  PhalconEye
 * @package   Core\Commands
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @CommandName(['package', 'pkg'])
 * @CommandDescription('Package management.')
 */
class PackageCommand extends AbstractCommand implements CommandInterface
{
    /**
     * Generate package in app folder.
     *
     * @param string $type Type of package to generate. Allowed types: module, plugin, widget, theme.
     *
     * @return void
     */
    public function generateAction($type)
    {
        if (!$this->_checkType($type)) {
            print ConsoleUtils::error("Wrong package type '$type'. Allowed types: module, plugin, widget, theme.") .
                PHP_EOL;
            return;
        }

        $packageManager = new Manager();
        $data = $this->_collectGenerationData($type);

        try {
            $packageManager->createPackage($data);
        } catch (PackageException $ex) {
            print ConsoleUtils::error($ex->getMessage()) . PHP_EOL;
            return;
        }

        print PHP_EOL . ConsoleUtils::success("Package generation completed!") . PHP_EOL;
    }

    /**
     * Check type.
     *
     * @param string $type Type of package to generate.
     *
     * @return bool
     */
    private function _checkType($type)
    {
        return in_array($type, array_keys(Manager::$allowedTypes));
    }

    /**
     * Collect data from input about new package.
     *
     * @param string $type Type of package to generate.
     *
     * @return array
     */
    private function _collectGenerationData($type)
    {
        $data = ['type' => $type];
        $data['name'] = $this->_readline(
            "Package name (lower case): ",
            new StringLength(['messageMinimum' => 'Name is too short. Minimum length is 3.', "min" => 3])
        );
        $data['nameUpper'] = ucfirst($data['name']);

        //@TODO:
        // - if widget - as about module

        return $data;
    }
}