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

require_once "./public/init.php";
require_once ROOT_PATH . "/core/engine/Config.php";
require_once ROOT_PATH . "/core/engine/Exception.php";
require_once ROOT_PATH . "/core/engine/Behavior/ApplicationBehavior.php";
require_once ROOT_PATH . "/core/engine/Application.php";
require_once ROOT_PATH . "/core/engine/Cli.php";

$application = new Engine\Cli();
$application->run();
echo $application->getOutput();

