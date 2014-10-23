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

namespace Engine;

use Engine\Behaviour\DIBehaviour;
use Phalcon\DI;

/**
 * Installer.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class Installer
{
    use DIBehaviour;

    /**
     * Used to install specific database entities or other specific action.
     *
     * @return void
     */
    public abstract function install();

    /**
     * Used before package will be removed from the system.
     *
     * @return void
     */
    public abstract function remove();

    /**
     * Used to apply some updates.
     *
     * @param string $currentVersion Current version name.
     *
     * @return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public abstract function update($currentVersion);

    /**
     * Execute sql file.
     *
     * @param string $filePath SQL file path.
     *
     * @throws Exception
     * @return void
     */
    public function runSqlFile($filePath)
    {
        if (file_exists($filePath)) {
            $connection = $this->getDI()->get('db');
            $connection->begin();
            $connection->query(file_get_contents($filePath));
            $connection->commit();
        } else {
            throw new Exception(sprintf('Sql file "%s" does not exists', $filePath));
        }
    }
}