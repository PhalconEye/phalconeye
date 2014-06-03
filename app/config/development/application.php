<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

/**
* WARNING
*
* Manual changes to this file may cause a malfunction of the system.
* Be careful when changing settings!
*
*/

return array (
  'debug' => true,
  'profiler' => true,
  'baseUrl' => '/',
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
    'compileAlways' => true,
  ),
  'session' => 
  array (
    'adapter' => 'Files',
    'uniqueId' => 'PhalconEye_',
  ),
  'assets' => 
  array (
    'local' => 'assets/',
    'remote' => false,
    'lifetime' => 0,
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
  'languages' => 
  array (
    'cacheDir' => ROOT_PATH . '/app/var/cache/languages/',
  ),
);