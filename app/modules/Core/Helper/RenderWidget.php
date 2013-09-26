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

namespace Core\Helper;

class RenderWidget extends \Phalcon\Tag implements \Engine\HelperInterface
{
    static public function _(\Phalcon\DI $di, array $args){
        if (!self::isAllowed($args[1])) return '';

        $widget = new \Engine\Widget\Element($args[0], $args[1]);
        return $widget->render();
    }

    private static function isAllowed($params){
        $viewer = \User\Model\User::getViewer();
        if (empty($params['roles']) || !is_array($params['roles'])) return true;
        return in_array($viewer->role_id, $params['roles']);
    }
}