<?php

error_reporting(E_ALL);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

define('PE_VERSION', '0.3.0');


require_once ROOT_PATH . "/app/engine/Application.php";
require_once ROOT_PATH . "/app/engine/Error.php";


try {
    $application = new Engine\Application();
    $application->run();
    echo $application->getOutput();
} catch (Exception $e) {
    \Engine\Error::exception($e);
    throw $e;
}

