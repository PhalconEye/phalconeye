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

namespace Engine;

use Phalcon\DI;

abstract class Installer
{
    use DependencyInjection;

    /**
     * Used to install specific database entities or other specific action
     */
    public abstract function install();

    /**
     * Used before package will be removed from the system
     */
    public abstract function remove();

    /**
     * Used to apply some updates
     *
     * @param $currentVersion
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