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

namespace Engine\Db\Data;

/**
 * Update statement data.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UpdateStatementData
{
    const SEPARATOR = '_';

    const OBJ_TABLE = 'table';
    const OBJ_COLUMN = 'column';
    const OBJ_INDEX = 'index';
    const OBJ_REF = 'reference';

    const STMT_CREATE = 'create';
    const STMT_MODIFY = 'modify';
    const STMT_DROP = 'drop';

    private $_obj;
    private $_stmt;
    private $_failedCount = 0;
    private $_executedCount = 0;

    /**
     * UpdateStatementData constructor.
     *
     * @param string $obj  Object type.
     * @param string $stmt Statement type.
     */
    public function __construct($obj, $stmt)
    {
        $this->_obj = $obj;
        $this->_stmt = $stmt;
    }

    /**
     * Increment executed count.
     */
    public function incrementExecuted()
    {
        $this->_executedCount++;
    }

    /**
     * Increment failed count.
     */
    public function incrementFailed()
    {
        $this->_failedCount++;
    }

    /**
     * Get statement unique guid.
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->_obj . self::SEPARATOR . $this->_stmt;
    }

    /**
     * Get object type.
     *
     * @return mixed
     */
    public function getObj()
    {
        return $this->_obj;
    }

    /**
     * Get statement type.
     *
     * @return mixed
     */
    public function getStmt()
    {
        return $this->_stmt;
    }

    /**
     * Get failed count.
     *
     * @return mixed
     */
    public function getFailedCount()
    {
        return $this->_failedCount;
    }

    /**
     * Get success count.
     *
     * @return mixed
     */
    public function getExecutedCount()
    {
        return $this->_executedCount;
    }
}