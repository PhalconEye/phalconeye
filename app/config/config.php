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
    'engineDir' => ROOT_PATH . '/app/library/Engine/',
    'controllersDir' => ROOT_PATH . '/app/controllers/',
    'modelsDir' => ROOT_PATH . '/app/models/',
    'viewsDir' => ROOT_PATH . '/app/views/',
    'miscDir' => ROOT_PATH . '/app/misc/',
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
  ),
  'models' => 
  array (
    'metadata' => 
    array (
      'adapter' => 'Memory',
    ),
  ),
  'modules' => false,
));