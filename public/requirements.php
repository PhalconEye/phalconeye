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
*/
if (!defined('CHECK_REQUIREMENTS')) {
    die('Access denied!');
}

if (version_compare(phpversion(), PHP_VERSION_REQUIRED, '<')) {
    printf('PHP %s is required, you have %s.', PHP_VERSION_REQUIRED, phpversion());
    exit(1);
}
if (!extension_loaded('phalcon')) {
    printf('Install Phalcon framework %s', PHALCON_VERSION_REQUIRED);
    exit(1);
}
if (version_compare(phpversion('phalcon'), PHALCON_VERSION_REQUIRED, '<')) {
    printf('Phalcon Framework %s is required, you have %s.', PHALCON_VERSION_REQUIRED, phpversion('phalcon'));
    exit(1);
}

$checkPath = array(
    $this->_config->application->assets->local,
    $this->_config->application->logger->path,
    $this->_config->application->cache->cacheDir,
    $this->_config->application->view->compiledPath,
    $this->_config->metadata->metaDataDir,
    $this->_config->annotations->annotationsDir,
    ROOT_PATH . '/app/var/cache/languages/',
    ROOT_PATH . '/app/var/temp'
);

$GLOBALS['PATH_REQUIREMENTS'] = $checkPath;

$allPassed = true;

foreach ($checkPath as $path) {
    $is_writable = is_writable($path);
    if (!$is_writable) {
        echo "{$path} isn't writable.</br>";
    }

    $allPassed = $allPassed && $is_writable;
}

if (!$allPassed) {
    exit(1);
}
