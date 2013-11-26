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

namespace Engine\Helper;

use Engine\HelperInterface;
use Phalcon\DI;
use Phalcon\Tag;

/**
 * Paginator url helper.
 *
 * @category  PhalconEye
 * @package   Engine\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PaginatorUrl extends Tag implements HelperInterface
{
    /**
     * Execute helper.
     *
     * @param DI    $di   Dependency injection.
     * @param array $args Helper arguments.
     *
     * @return mixed
     */
    static public function _(DI $di, array $args)
    {
        $page = (isset($args[0]) ? $args[0] : 1);
        $vars = array();
        $url = '/';
        foreach ($_GET as $key => $get) {
            if ($key == '_url') {
                $url = $get;
                continue;
            }

            if ($key == 'page') {
                continue;
            }

            $vars[] = $key . '=' . $get;
        }
        unset($vars['_url']);

        if (count($vars) == 0) {
            if ($page) {
                $page = '?page=' . $page;
            }

            return $url . $page;
        }

        if ($page) {
            $page = '&page=' . $page;
        }

        return sprintf('%s?%s%s', $url, implode('&', $vars), $page);
    }
}