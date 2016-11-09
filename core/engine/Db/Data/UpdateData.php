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
 * Update data.
 * Stores executed statements counts by type.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class UpdateData
{
    private $_statements = [];
    private $_executedCount = 0;
    private $_failedCount = 0;

    /**
     * Add executed statement.
     *
     * @param string $obj     Object type.
     * @param string $stmt    Statement type.
     * @param bool   $success Statement failed?
     */
    public function add($obj, $stmt, $success = true)
    {
        $data = $this->_getOrCreate($obj, $stmt);
        $data->incrementExecuted();
        if (!$success) {
            $data->incrementFailed();
            $this->_failedCount++;
        }

        $this->_executedCount++;
    }

    /**
     * Get count of executed statements.
     *
     * @return int
     */
    public function getExecutedCount(): int
    {
        return $this->_executedCount;
    }

    /**
     * Get failed count.
     *
     * @return int
     */
    public function getFailedCount(): int
    {
        return $this->_failedCount;
    }

    /**
     * Get executed statements.
     *
     * @return UpdateStatementData[]
     */
    public function getStatements(): array
    {
        return $this->_statements;
    }

    /**
     * Check if was any statements executed.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getExecutedCount() == 0;
    }

    /**
     * Create data in statements is missing.
     *
     * @param string $obj  Object type.
     * @param string $stmt Statement type.
     *
     * @return UpdateStatementData;
     */
    private function _getOrCreate($obj, $stmt)
    {
        $key = $obj . UpdateStatementData::SEPARATOR . $stmt;
        if (isset($this->_statements[$key])) {
            return $this->_statements[$key];
        }

        $stmt = new UpdateStatementData($obj, $stmt);
        $this->_statements[$key] = $stmt;
        return $stmt;
    }
}