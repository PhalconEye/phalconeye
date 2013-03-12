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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Helper_PaginatorUrl extends \Phalcon\Tag
{
    static public function _($page = null){
        $vars = array();
        $url = '/';
        foreach($_GET as $key => $get){
            if ($key == '_url'){
                $url = $get;
                continue;
            }

            if ($key == 'page'){
                continue;
            }

            $vars[] = $key . '='. $get;
        }
        unset($vars['_url']);

        if (count($vars) == 0){
            if ($page){
                $page = '?page='.$page;
            }
            return  $url . $page;
        }

        if ($page){
            $page = '&page='.$page;
        }

        return sprintf('%s?%s%s', $url, implode('&', $vars), $page);
    }
}