<?php

/**
 * Provides small layer between session and services
 */
class Api_Auth{

    private $_identity = 0;

    /**
     * @param $identity Current session identity
     */
    public function __construct($identity){
        $this->_identity = $identity;
    }

    /**
     * Authenticate user
     *
     * @param int
     *
     * @return bool
     */
    public function authenticate($identity){
        $_SESSION['identity'] = $this->_identity = $identity;
    }

    /**
     * Clear identity, logout
     */
    public function clearAuth(){
        $_SESSION['identity'] = $this->_identity = 0;
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