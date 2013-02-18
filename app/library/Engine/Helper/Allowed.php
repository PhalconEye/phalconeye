<?php

class Helper_Allowed extends \Phalcon\Tag
{
    static public function _($where, $what){
        $viewer = User::getViewer();
        return Phalcon\DI::getDefault()->get('acl')->_()->isAllowed($viewer->getRole()->getName(), $where, $what) == \Phalcon\Acl::ALLOW;
    }
}