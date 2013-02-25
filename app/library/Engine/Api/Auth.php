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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

/**
 * Provides small layer between session and services
 */
class Api_Auth extends \Phalcon\Mvc\User\Plugin{

    private $_identity = 0;

    /**
     * @param $identity Current session identity
     */
    public function __construct($di){
        $this->_dependencyInjector = $di;
        $this->_identity = $this->session->get('identity', 0);
    }

    /**
     * Authenticate user
     *
     * @param int
     *
     * @return bool
     */
    public function authenticate($identity){
        $this->_identity = $identity;
        $this->session->set('identity', $identity);
    }

    /**
     * Clear identity, logout
     */
    public function clearAuth(){
        $this->_identity = 0;
        $this->session->set('identity', 0);
    }

    /**
     * Get current identity
     *
     * @return int
     */
    public function getIdentity(){
        return $this->_identity;
    }

}