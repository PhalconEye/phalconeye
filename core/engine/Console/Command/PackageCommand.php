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
 | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
 +------------------------------------------------------------------------+
*/

namespace Engine\Console\Command;

use Engine\Console\Command\Validation\WidgetModuleValidator;
use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Package\PackageManager;
use Engine\Utils\ConsoleUtils;
use Engine\Package\PackageGenerator;
use Engine\Package\PackageException;
use Phalcon\Validation\Validator\Regex;
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
            print ConsoleUtils::errorLine("Wrong package type '$type'. Allowed types: module, plugin, widget, theme.") .
                PHP_EOL;
            return;
        }

        $packageManager = new PackageGenerator();
        $data = $this->_collectData($type);

        try {
            $packageManager->createPackage($data);
        } catch (PackageException $ex) {
            print ConsoleUtils::errorLine($ex->getMessage()) . PHP_EOL;
            return;
        }

        print PHP_EOL . ConsoleUtils::successLine("Package generation completed!") . PHP_EOL;
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
        return in_array($type, array_keys(PackageManager::$ALLOWED_TYPES));
    }

    /**
     * Collect data from input about new package.
     *
     * @param string $type Type of package to generate.
     *
     * @return array
     */
    private function _collectData($type)
    {
        switch ($type) {
            case PackageManager::PACKAGE_TYPE_MODULE:
                return $this->_collectDataForModule();

            case PackageManager::PACKAGE_TYPE_WIDGET:
                return $this->_collectDataForWidget();

            case PackageManager::PACKAGE_TYPE_PLUGIN:
                return $this->_collectDataForPlugin();

            case PackageManager::PACKAGE_TYPE_THEME:
                return $this->_collectDataForTheme();
        }

        return [];
    }

    /**
     * Collect data for module.
     *
     * @return array Collected data.
     */
    private function _collectDataForModule()
    {
        $data = ['type' => PackageManager::PACKAGE_TYPE_MODULE];
        $data['name'] = $this->_readline(
            "Package name: ",
            [
                new StringLength(['messageMinimum' => 'Name is too short. Minimum length is 3.', "min" => 3]),
                new Regex(['message' => 'Name must be in lowercase, only letters.', 'pattern' => '/^[a-z]+$/'])
            ]
        );
        $data['nameUpper'] = ucfirst($data['name']);

        return $data;
    }

    /**
     * Collect data for widget.
     *
     * @return array Collected data.
     */
    private function _collectDataForWidget()
    {
        $data = ['type' => PackageManager::PACKAGE_TYPE_WIDGET];
        $data['name'] = $this->_readline(
            "Package name: ",
            [
                new StringLength(['messageMinimum' => 'Name is too short. Minimum length is 3.', "min" => 3])
            ]
        );
        $data['nameUpper'] = ucfirst($data['name']);
        $data['module'] = $this->_readline(
            "Module name (leave empty for external package): ",
            [
                new WidgetModuleValidator($this->getDI())
            ]
        );

        return $data;
    }

    /**
     * Collect data for plugin.
     *
     * @return array Collected data.
     */
    private function _collectDataForPlugin()
    {
        $data = $this->_collectDataForWidget();
        $data['type'] = PackageManager::PACKAGE_TYPE_PLUGIN;
        return $data;
    }

    /**
     * Collect data for theme.
     *
     * @return array Collected data.
     */
    private function _collectDataForTheme()
    {
        $data = $this->_collectDataForModule();
        $data['type'] = PackageManager::PACKAGE_TYPE_THEME;
        return $data;
    }
}