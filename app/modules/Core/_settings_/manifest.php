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
        'name' => 'core',
        'version' => PE_VERSION,
        'repository' => 'phalconeye.com',
        'title' => 'Core',
        'description' => 'PhalconEye Core',
        'author' => 'PhalconEye Team',
        'callback' => array(
            'path' => '_settings_/install.php',
            'class' => '\Core\_settings_\Installer',
        )
    )
);