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
class Extension
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
            case 'helper':
                return '\Engine\Helper::getInstance(' . $arguments . ')';

            case 'classof':
                return 'get_class(' . $arguments . ')';

            case 'instanceof':
                $resolvedArgs = explode(',', $arguments);
                $resolvedArgs[1] = trim(str_replace(["'", '"'], ['', ''], $resolvedArgs[1]));
                return $resolvedArgs[0] . ' instanceof ' . $resolvedArgs[1];

            case 'resolveView':
                $value = $params[0]['expr']['value'];
                if (isset($params[1])) {
                    $value = '../../' .
                        ucfirst($params[1]['expr']['value']) . '/View/' . $value;
                }
                return "'" . $value . "'";
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
            case 'trans':
                return '$this->trans->query(' . $arguments . ')';
        }
    }
}