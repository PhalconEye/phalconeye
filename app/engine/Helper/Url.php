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

namespace Engine\Helper;

use Engine\Helper;
use Phalcon\DI;
use Phalcon\Tag;

/**
 * Current url helper.
 *
 * @category  PhalconEye
 * @package   Engine\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Url extends Helper
{
    /**
     * Get current url.
     *
     * @return mixed
     */
    public function currentUrl()
    {
        return $this->getDI()->get('request')->get('_url');
    }

    /**
     * Get url for paginator.
     *
     * @param null|int $pageNumber Current page number.
     *
     * @return string
     */
    public function paginatorUrl($pageNumber = null)
    {
        $page = (!empty($pageNumber) ? $pageNumber : 1);
        list ($url, $vars) = $this->_decomposeParams();
        $url = '/' . $url;

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

    /**
     * Decompose $_GET params to a url and vars.
     *
     * @param array|null $params Params to decompose.
     *
     * @return array
     */
    private function _decomposeParams($params = null)
    {
        $vars = [];
        $url = '';

        if (!$params) {
            $params = $_GET;
        }

        foreach ($params as $key => $get) {
            if (is_array($get)) {
                list ($url1, $vars1) = $this->_decomposeParams($get);
                $url .= $url1;
                $vars += $vars1;

                continue;
            }
            if ($key == '_url') {
                $url = $get;
                continue;
            }

            if ($key == 'page') {
                continue;
            }

            $vars[] = $key . '=' . $get;
        }

        return array($url, $vars);
    }
}