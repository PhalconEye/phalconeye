<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine;

use Phalcon\DI;

/**
 * Helper class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Helper
{
    /**
     * Current module name.
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Helper constructor.
     *
     * @param string $module Module name
     */
    public function __construct($module)
    {
        $this->_moduleName = $module;
    }

    /**
     * Call helper through magic method.
     *
     * @param string $name      Helper name.
     * @param array  $arguments Helper arguments.
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        /** @var HelperInterface $helperClassName */
        $helperClassName = sprintf('\%s\Helper\%s', ucfirst($this->_moduleName), ucfirst($name));
        if (!class_exists($helperClassName)) {
            throw new \Engine\Exception(sprintf('Can not find Helper with name "%s".', $name));
        }

        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }

        // Collect profile info.
        $di = DI::getDefault();
        $config = $di->get('config');
        $profilerIsActive = $config->application->debug && $di->has('profiler');
        if ($profilerIsActive) {
            $di->get('profiler')->start();
        }

        $content = $helperClassName::_($di, $arguments);

        // collect profile info
        if ($profilerIsActive) {
            $di->get('profiler')->stop($helperClassName, 'helper');
        }

        return $content;
    }

}