<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

namespace Engine\View;

use Phalcon\DI;
use Phalcon\Mvc\Router;

/**
 * Volt function extension.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Extension extends DI\Injectable
{
    /**
     * Compile functions for volt.
     *
     * @param string $name      Function name.
     * @param string $arguments Function arguments as string.
     * @param array  $params    Function parsed params.
     *
     * @return string|void
     */
    public function compileFunction($name, $arguments, $params)
    {
        switch ($name) {
            case 'php':
                $code = '';
                if (isset($params[0]) && isset($params[0]['expr']['value'])) {
                    $code = $params[0]['expr']['value'];
                }
                return $code;

            case 'helper':
                return '\Engine\Helper::getInstance(' . $arguments . ')';

            case 'classof':
                return 'get_class(' . $arguments . ')';

            case 'instanceof':
                $resolvedArgs = explode(',', $arguments);
                $resolvedArgs[1] = trim(str_replace(["'", '"'], ['', ''], $resolvedArgs[1]));
                return $resolvedArgs[0] . ' instanceof ' . $resolvedArgs[1];

            case 'resolveView':

                if (isset($params[1])) {
                    $value = $this->_resolveView($params[0]['expr']['value'], $params[1]['expr']['value']);
                } else {
                    $value = $this->_resolveView(
                        $params[0]['expr']['value'],
                        $this->getDI()->getRouter()->getModuleName()
                    );
                }
                return "'" . $value . "'";

            case 'partial':
                if (!isset($params[0]['expr']['value'])) {
                    return '$this->partial(' . $arguments . ')';
                }

                if (isset($params[2])) {
                    $value = $this->_resolveView($params[0]['expr']['value'], $params[2]['expr']['value']);
                    $arguments = substr($arguments, 0, strripos($arguments, ','));
                } else {
                    $value = $this->_resolveView(
                        $params[0]['expr']['value'],
                        $this->getDI()->getRouter()->getModuleName()
                    );
                }

                $arguments = str_replace($params[0]['expr']['value'], $value, $arguments);
                return '$this->partial(' . $arguments . ')';
        }
    }

    /**
     * Compile filters for volt.
     *
     * @param string $name      Function name.
     * @param string $arguments Function arguments as string.
     *
     * @return string|void
     */
    public function compileFilter($name, $arguments)
    {
        switch ($name) {
            case 'i18n':
                return '$this->i18n->query(' . $arguments . ')';
        }
    }

    /**
     * Resolve view, according to module.
     *
     * @param string $view   View path.
     * @param string $module Module name.
     *
     * @return string
     */
    protected function _resolveView($view, $module)
    {
        return '../../' . ucfirst($module) . '/View/' . $view;
    }
}