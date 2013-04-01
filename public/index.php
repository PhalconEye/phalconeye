<?php

error_reporting(E_ALL);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

define('PE_VERSION', '0.2.0');



require_once ROOT_PATH . "/app/library/Engine/Application.php";

$application = new Application();
$application->run();
echo $application->getOutput();

