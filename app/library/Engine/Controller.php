<?php

class Controller extends \Phalcon\Mvc\Controller
{

    /**
     * Initializes the controller
     */
    public function initialize()
    {
        // run init function
        if (method_exists($this, 'init'))
            $this->init();
    }

}