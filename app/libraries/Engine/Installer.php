<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

class Installer
{
    const PACKAGE_TYPE_MODULE = 'module';
    const PACKAGE_TYPE_PLUGIN = 'plugin';
    const PACKAGE_TYPE_THEME = 'theme';
    const PACKAGE_TYPE_WIDGET = 'widget';

    public static $allowedTypes = array(
        self::PACKAGE_TYPE_MODULE,
        self::PACKAGE_TYPE_PLUGIN,
        self::PACKAGE_TYPE_THEME,
        self::PACKAGE_TYPE_WIDGET
    );
}