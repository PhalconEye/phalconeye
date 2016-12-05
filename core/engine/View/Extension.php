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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\View;

use Engine\Behavior\ViewBehavior;
use Phalcon\DI;
use Phalcon\Mvc\Router;

/**
 * Volt function extension.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Extension extends DI\Injectable
{
    use ViewBehavior;

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
                return '\Engine\Helper\AbstractHelper::getInstance(' . $arguments . ')';

            case 'classof':
                return 'get_class(' . $arguments . ')';

            case 'instanceof':
                $resolvedArgs = explode(',', $arguments);
                $resolvedArgs[1] = trim(str_replace(["'", '"'], ['', ''], $resolvedArgs[1]));
                return $resolvedArgs[0] . ' instanceof ' . $resolvedArgs[1];
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
}