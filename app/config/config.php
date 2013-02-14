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
    'engineDir' => '/www/phalcon/www/app/library/Engine/',
    'controllersDir' => '/www/phalcon/www/app/controllers/',
    'modelsDir' => '/www/phalcon/www/app/models/',
    'viewsDir' => '/www/phalcon/www/app/views/',
    'miscDir' => '/www/phalcon/www/app/misc/',
    'cache' => 
    array (
      'lifetime' => '86400',
      'adapter' => 'Memcache',
      'host' => '127.0.0.1',
      'port' => '11211',
      'persistent' => '1',
    ),
    'logger' => 
    array (
      'enabled' => true,
      'path' => '/www/phalcon/www/app/var/logs/',
      'format' => '[%date%][%type%] %message%',
    ),
    'view' => 
    array (
      'compiledPath' => '/www/phalcon/www/app/var/compiled/',
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
  'router' => 
  array (
    '/admin' => 
    array (
      'controller' => 'admin-index',
      'action' => 'index',
      'name' => 'admin',
      'params' => 1,
    ),
    '/admin/{admin_controller}/:params' => 
    array (
      'controller' => 'admin-index',
      'action' => 'index',
      'name' => 'admin',
      'params' => 1,
    ),
    '/admin/{admin_controller}/{admin_action}/:params' => 
    array (
      'controller' => 'admin-index',
      'action' => 'index',
      'name' => 'admin',
      'params' => 1,
    ),
    '/admin/{admin_controller}/{admin_action}/{admin_id}/:params' => 
    array (
      'controller' => 'admin-index',
      'action' => 'index',
      'name' => 'admin',
      'params' => 1,
    ),
    '/page/([a-z\\-|0-9]+)' => 
    array (
      'controller' => 'page',
      'action' => 'index',
      'url' => 1,
      'name' => 'custom_page',
    ),
    '/login' => 
    array (
      'controller' => 'auth',
      'action' => 'login',
      'name' => 'login',
    ),
    '/logout' => 
    array (
      'controller' => 'auth',
      'action' => 'logout',
      'name' => 'logout',
    ),
    '/register' => 
    array (
      'controller' => 'auth',
      'action' => 'register',
      'name' => 'register',
    ),
  ),
));