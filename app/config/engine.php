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

/**
* WARNING
*
* Manual changes to this file may cause a malfunction of the system.
* Be careful when changing settings!
*
*/

return new \Phalcon\Config(array (
  'installed' => false,
  'installedVersion' => null,
  'database' =>
  array (
    'adapter' => 'Mysql',
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'phalconeye',
  ),
  'application' =>
  array (
    'debug' => false,
    'profiler' => true,
    'baseUri' => '/',
    'engineDir' => ROOT_PATH . '/app/engine/',
    'modulesDir' => ROOT_PATH . '/app/modules/',
    'pluginsDir' => ROOT_PATH . '/app/plugins/',
    'widgetsDir' => ROOT_PATH . '/app/widgets/',
    'librariesDir' => ROOT_PATH . '/app/libraries/',
    'cache' =>
    array (
      'lifetime' => '86400',
      'prefix' => 'pe_',
      'adapter' => 'File',
      'cacheDir' => ROOT_PATH . '/app/var/cache/data/',
    ),
    'logger' =>
    array (
      'enabled' => true,
      'path' => ROOT_PATH . '/app/var/logs/',
      'format' => '[%date%][%type%] %message%',
    ),
    'view' =>
    array (
      'compiledPath' => ROOT_PATH . '/app/var/cache/view/',
      'compiledExtension' => '.php',
      'compiledSeparator' => '_',
      'compileAlways' => true
    ),
    'assets' =>
    array (
      'local' => 'assets/',
      'remote' => false,
    ),
  ),
  'metadata' =>
  array (
    'adapter' => 'Files',
    'metaDataDir' => ROOT_PATH . '/app/var/cache/metadata/',
  ),
  'annotations' =>
  array (
    'adapter' => 'Files',
    'annotationsDir' => ROOT_PATH . '/app/var/cache/annotations/',
  ),
  'modules' =>
  array (
  ),
  'events' => 
  array (
  ),
  'plugins' => 
  array (
  ),
));