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

return new \Phalcon\Config(array(
        'type' => 'module',
        'name' => 'blog',
        'version' => '1.0.0',
        'repository' => 'phalconeye.com',
        'title' => 'Blogs',
        'description' => 'Blogs',
        'author' => 'PhalconEye Team',
        'dependencies' => array(
            array(
                'name' => 'core',
                'minVersion' => '0.4.0',
            ),
        ),
        'callback' => array(
            'path' => '_settings_/install.php',
            'class' => 'Blog\Installer',
        ),
        'hooks' => array(
            'view' => array(
                'event' => 'view:renderBefore',
                'resource' => 'Blog\Plugin\View'
            ),
            'dispatcher' => array(
                'event' => 'dispatch:beforeException',
                'resource' => 'Blog\Plugin\Dispatcher',
            ),
        ),
    )
);