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
/**
 * Stages.
 */
define('APPLICATION_STAGE_DEVELOPMENT', 'development');
define('APPLICATION_STAGE_PRODUCTION', 'production');
define('APPLICATION_STAGE', (getenv('PHALCONEYE_STAGE') ? getenv('PHALCONEYE_STAGE') : APPLICATION_STAGE_PRODUCTION));

if (APPLICATION_STAGE == APPLICATION_STAGE_DEVELOPMENT) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

/**
 * Versions.
 */
define('PHALCONEYE_VERSION', '0.5.0');
define('PHALCON_VERSION_REQUIRED', '3.0.1');
define('PHP_VERSION_REQUIRED', '7.0.11');

/**
 * Check phalcon framework installation.
 */
if (!extension_loaded('phalcon')) {
    printf('Install Phalcon framework %s', PHALCON_VERSION_REQUIRED);
    exit(1);
}

/**
 * Pathes.
 */
define('DS', DIRECTORY_SEPARATOR);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', dirname(__FILE__));
}
