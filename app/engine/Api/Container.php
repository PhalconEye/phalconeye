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

namespace Engine\Api;

class Container
{
    protected $_moduleName;
    protected $_di;

    public function __construct($moduleName, $di)
    {
        $this->_moduleName = $moduleName;
        $this->_di = $di;
    }

    public function __call($name, $arguments){
        $apiClassName = sprintf('\%s\Api\%s', ucfirst($this->_moduleName), ucfirst($name));
        if (!class_exists($apiClassName)){
            throw new \Engine\Exception(sprintf('Can not find Api with name "%s".', $name));
        }

        return new $apiClassName($this->_di);
    }

}