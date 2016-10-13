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
        require_once ROOT_PATH . "/app/engine/Config.php";
        require_once ROOT_PATH . "/app/engine/Exception.php";
        require_once ROOT_PATH . "/app/engine/ApplicationInitialization.php";
        require_once ROOT_PATH . "/app/engine/Application.php";
        $application = new \Engine\Application();
        $application->run('session');

        $identity = Phalcon\DI::getDefault()->get('session')->get('identity');
        $viewer = \User\Model\User::findFirstById($identity);
        if (!$viewer || !$viewer->isAdmin()) {
            die('Access not allowed');
        }
    }
}
?>