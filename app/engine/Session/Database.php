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

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Engine\Session;

/**
 * Phalcon\Session\Adapter\Database
 *
 * Database adapter for Phalcon\Session
 */
class Database extends \Phalcon\Session\Adapter implements \Phalcon\Session\AdapterInterface
{

    protected $_lifetime = 1440; // 24 mins

    /**
     * Phalcon\Session\Adapter\Database constructor
     *
     * @param array $options
     */
    public function __construct($options = null)
    {

        if (!isset($options['db'])) {
            throw new \Engine\Exception("The parameter 'db' is required");
        }

        if (!isset($options['table'])) {
            throw new \Engine\Exception("The parameter 'table' is required");
        }

        if (isset($options['lifetime'])) {
            $this->_lifetime = (int)$options['lifetime'];
        } else {
            $this->_lifetime = (int)ini_get('session.gc_maxlifetime');
        }

        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );

        parent::__construct($options);
    }


    public function open()
    {
        return true;
    }

    public function close()
    {
        return false;
    }

    /**
     * Reads the data from the table
     *
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        $options = $this->getOptions();
        $sessionData = $options['db']->fetchOne("SELECT * FROM " . $options['table'] . " WHERE session_id = '" . $sessionId . "'");
        if ($sessionData) {
            return $sessionData['data'];
        }
    }

    /**
     * Writes the data to the table
     *
     * @param string $sessionId
     * @param string $data
     */
    public function write($sessionId, $data)
    {
        $options = $this->getOptions();
        $data =  mysql_real_escape_string($data);
        $exists = $options['db']->fetchOne("SELECT COUNT(*) FROM " . $options['table'] . " WHERE session_id = '" . $sessionId . "'");
        if ($exists[0]) {
            $options['db']->execute("UPDATE " . $options['table'] . " SET data = '" . $data . "', modification_date = " . time() . " WHERE session_id = '" . $sessionId . "'");
        } else {
            $options['db']->execute("INSERT INTO " . $options['table'] . " VALUES ('" . $sessionId . "', '" . $data . "', " . time() . ", 0)");
        }
    }

    /**
     * Destroyes the session
     *
     */
    public function destroy()
    {
        $options = $this->getOptions();
        $options['db']->execute("DELETE FROM " . $options['table'] . " WHERE session_id = '" . session_id() . "'");
    }

    /**
     * Performs garbage-collection on the session table
     *
     */
    public function gc()
    {
        $options = $this->getOptions();
        $options['db']->execute("DELETE FROM " . $options['table'] . " WHERE UNIX_TIMESTAMP() - `modification_date` > {$this->_lifetime} AND UNIX_TIMESTAMP() - `creation_date` > {$this->_lifetime}");

        return true;
    }

}