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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\Migration\Model;

use Engine\Db\AbstractModel;

/**
 * Migration.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("migration")
 */
class MigrationModel extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="module", size="20")
     */
    public $module;

    /**
     * @Column(type="string", nullable=false, column="version", size="50")
     */
    public $version;

    /**
     * @Column(type="datetime", nullable=true, column="creation_date")
     */
    public $creation_date;

    /**
     * Get last migrated versions for each modules.
     *
     * @param string $module Module name.
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function findModuleMigrations($module)
    {
        return self::query()
            ->columns(['version'])
            ->where('module  = :module:', ['module' => $module])
            ->execute();
    }
}
