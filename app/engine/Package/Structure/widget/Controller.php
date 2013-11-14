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
*/

%header%

namespace Widget\%nameUpper%;

class Controller extends \Engine\Widget\Controller
{

    public function indexAction()
    {

    }

    public function adminAction(){
        $form = new \Engine\Form();

        return $form;
    }

    public function isCached(){
        return false;
    }

    public function cacheLifeTime(){
        return 300;
    }
}