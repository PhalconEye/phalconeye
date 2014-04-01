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
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Install;

use Core\Form\CoreForm;

/**
 * Installation database form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Install
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Database extends CoreForm
{
    /**
     * Setup form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setTitle('Database settings');

        $this->addContentFieldSet()
            ->addSelect(
                'adapter',
                'Database adapter',
                null,
                [
                    'Mysql' => 'MySQL',
                    'Oracle' => 'Oracle',
                    'Postgresql' => 'PostgreSQL',
                    'Sqlite' => 'SQLite'
                ],
                'Mysql'
            )
            ->addText('host', 'Database host', null, 'localhost')
            ->addText('port', 'Database port', null, '3306')
            ->addText('username', 'Username', null, 'root')
            ->addPassword('password')
            ->addText('dbname', 'Database name', null, 'phalconeye');

        $this->addFooterFieldSet()->addButton('next');
    }
}