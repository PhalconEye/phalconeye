<?php 

return new \Phalcon\Config(array (
  'database' => 
  array (
    'adapter' => 'Mysql',
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'name' => 'phalcon',
  ),
  'application' => 
  array (
    'debug' => true,
    'baseUri' => '/',
    'engineDir' => ROOT_PATH . '/app/libraries/Engine/',
    'modulesDir' => ROOT_PATH . '/app/modules/',
    'defaultModule' => 'core',
    'cache' => 
    array (
      'lifetime' => '86400',
      'prefix' => 'pe_',
      'adapter' => 'File',
      'cacheDir' => ROOT_PATH . '/app/var/cache/',
    ),
    'logger' => 
    array (
      'enabled' => true,
      'path' => ROOT_PATH . '/app/var/logs/',
      'format' => '[%date%][%type%] %message%',
    ),
    'view' => 
    array (
      'compiledPath' => ROOT_PATH . '/app/var/compiled/',
      'compiledExtension' => '.compiled',
    ),
    'session' => 
    array (
      'tableName' => 'session_data',
      'lifetime' => 1440,
    ),
  ),
  'models' => 
  array (
    'metadata' => 
    array (
      'adapter' => 'Apc',
    ),
  ),
  'modules' => 
  array (
    'blog' => true,
  ),
));