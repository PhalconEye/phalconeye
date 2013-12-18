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
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Install;

use Engine\Form;

/**
 * Installation database form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Install
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Database extends Form
{
    /**
     * Setup form.
     *
     * @return void
     */
    public function init()
    {
        $this->setOption('title', 'Database settings');

        $this->addElement(
            'select',
            'adapter',
            [
                'label' => 'Database adapter',
                'options' => [
                    'Mysql' => 'MySQL',
                    'Oracle' => 'Oracle',
                    'Postgresql' => 'PostgreSQL',
                    'Sqlite' => 'SQLite'
                ],
                'value' => 'Mysql'
            ]
        );

        $this->addElement(
            'text',
            'host',
            [
                'label' => 'Database host',
                'value' => 'localhost'
            ]
        );

        $this->addElement(
            'text',
            'username',
            [
                'label' => 'Username',
                'value' => 'root'
            ]
        );

        $this->addElement(
            'password',
            'password',
            [
                'label' => 'Password',
            ]
        );

        $this->addElement(
            'text',
            'dbname',
            [
                'label' => 'Database name',
                'value' => 'phalconeye'
            ]
        );

        $this->addButton('Continue', true);
    }
}