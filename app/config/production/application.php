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

return [
    'debug' => true,
    'profiler' => true,
    'baseUri' => '/',
    'cache' =>
        [
            'lifetime' => '86400',
            'prefix' => 'pe_',
            'adapter' => 'File',
            'path' => ROOT_PATH . '/app/var/cache/data/',
        ],
    'logger' =>
        [
            'enabled' => true,
            'path' => ROOT_PATH . '/app/var/logs/',
            'format' => '[%date%][%type%] %message%',
        ],
    'view' =>
        [
            'compiledPath' => ROOT_PATH . '/app/var/cache/view/',
            'compiledExtension' => '.php',
            'compiledSeparator' => '_',
            'compileAlways' => true,
        ],
    'session' =>
        [
            'adapter' => 'Files',
            'uniqueId' => 'PhalconEye'
        ],
    'assets' =>
        [
            'local' => 'assets/',
            'remote' => false,
        ],
    'metadata' =>
        [
            'adapter' => 'Files',
            'path' => ROOT_PATH . '/app/var/cache/metadata/',
        ],
    'annotations' =>
        [
            'adapter' => 'Files',
            'path' => ROOT_PATH . '/app/var/cache/annotations/',
        ]
];