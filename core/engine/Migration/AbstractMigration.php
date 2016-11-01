<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  +------------------------------------------------------------------------+
*/

namespace Engine\Migration;
use Engine\Behavior\DIBehavior;

/**
 * Migration.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractMigration
{
    use DIBehavior;

    /**
     * Run migration code.
     *
     * @return mixed
     */
    abstract function run();

    /**
     * Execute sql file.
     *
     * @param string $filePath SQL file path.
     *
     * @throws \RuntimeException File not found exception.
     * @return void
     */
    public function executeFile($filePath)
    {
        if (file_exists($filePath)) {
            $this->getDI()->getDb()->query(file_get_contents($filePath));
        } else {
            throw new \RuntimeException(sprintf('Sql file "%s" does not exists', $filePath));
        }
    }

    /**
     * Execute sql query.
     *
     * @return void
     */
    public function execute($query)
    {
        $this->getDI()->getDb()->query($query);
    }
}