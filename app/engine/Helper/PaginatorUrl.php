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

namespace Engine\Helper;

class PaginatorUrl extends \Phalcon\Tag implements \Engine\HelperInterface
{
    static public function _(\Phalcon\DI $di, array $args)
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