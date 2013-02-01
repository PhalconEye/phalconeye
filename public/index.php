<?php

error_reporting(E_ALL);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/app/library/Engine/Application.php";

$application = new Application();

echo $application->run();

