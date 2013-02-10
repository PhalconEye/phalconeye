<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'name' => 'phalcon',
    ),
    'application' => array(
        'debug' => true,
        'baseUri' => '/',
        'engineDir' => ROOT_PATH . '/app/library/Engine/',
        'controllersDir' => ROOT_PATH . '/app/controllers/',
        'modelsDir' => ROOT_PATH . '/app/models/',
        'viewsDir' => ROOT_PATH . '/app/views/',
        'miscDir' => ROOT_PATH . '/app/misc/',
        'cache' => array(
            'cacheDir' => ROOT_PATH . '/app/var/cache/',
            'lifetime' => 86400
        ),
        'logger' => array(
            'enabled' => true,
            'path' => ROOT_PATH . '/app/var/logs/',
            'format' => '[%date%][%type%] %message%'
        ),
        'view' => array(
            'compiledPath' => ROOT_PATH . '/app/var/compiled/',
            'compiledExtension' => '.compiled'
        )
    ),
    'models' => array(
        'metadata' => array(
            'adapter' => 'Memory'
        )
    ),
    'modules' => false,
    'router' => array(
        '/admin' => array(
            'controller' => 'admin-index',
            'action' => 'index',
            'name' => 'admin',
            'params' => 1
        ),
        '/admin/{admin_controller}/:params' => array(
            'controller' => 'admin-index',
            'action' => 'index',
            'name' => 'admin',
            'params' => 1
        ),
        '/admin/{admin_controller}/{admin_action}/:params' => array(
            'controller' => 'admin-index',
            'action' => 'index',
            'name' => 'admin',
            'params' => 1
        ),
        '/admin/{admin_controller}/{admin_action}/{admin_id}/:params' => array(
            'controller' => 'admin-index',
            'action' => 'index',
            'name' => 'admin',
            'params' => 1
        ),
        '/page/([a-z\-|0-9]+)' => array(
            'controller' => 'page',
            'action' => 'index',
            'url' => 1,
            'name' => 'custom_page',
        ),
        '/login' => array(
            'controller' => 'auth',
            'action' => 'login',
            'name' => 'login',
        ),
        '/logout' => array(
            'controller' => 'auth',
            'action' => 'logout',
            'name' => 'logout',
        ),
        '/register' => array(
            'controller' => 'auth',
            'action' => 'register',
            'name' => 'register',
        ),
    )
));
