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

defined('AJXP_EXEC') or die('Access not allowed');

/**
 * @package info.ajaxplorer.plugins
 * Standard auth implementation, stores the data in serialized files
 */
class phalconeyeAuthDriver extends AbstractAuthDriver
{
    var $driverName = "phalconeye";

    function init($options)
    {
        parent::init($options);

        // run Phalcon Eye to get session from database
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
        }

        require_once $_SERVER['DOCUMENT_ROOT'] . "/app/library/Engine/Application.php";
        $application = new Application();
        $application->run('mini');

        $identity = Phalcon\DI::getDefault()->get('session')->get('identity');
        if ($identity === null || empty($identity)){
            die('401 Authorization Required');
        }

    }

}

?>