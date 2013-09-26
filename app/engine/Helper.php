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

class Helper
{
    protected $_moduleName = null;

    public function __construct($module){
        $this->_moduleName = $module;
    }

    public function __call($name, $arguments)
    {
        /** @var HelperInterface $helperClassName  */
        $helperClassName = sprintf('\%s\Helper\%s', ucfirst($this->_moduleName), ucfirst($name));
        if (!class_exists($helperClassName)) {
            throw new \Engine\Exception(sprintf('Can not find Helper with name "%s".', $name));
        }

        if (!is_array($arguments))
            $arguments = array($arguments);

        // collect profile info
        $di = \Phalcon\DI::getDefault();
        $config = $di->get('config');
        $profilerIsActive = $config->application->debug && $di->has('profiler');
        if ($profilerIsActive){
            $di->get('profiler')->start();
        }

        $content = $helperClassName::_($di, $arguments);

        // collect profile info
        if ($profilerIsActive){
            $di->get('profiler')->stop($helperClassName, 'helper');
        }

        return $content;
    }

}