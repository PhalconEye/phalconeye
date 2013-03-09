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

class Helper_GetAllowed extends \Phalcon\Tag
{
    static public function _($where, $what){
        $viewer = User::getViewer();
        return Phalcon\DI::getDefault()->get('acl')->getAllowedValue($where, $viewer->getRole(), $what);
    }
}