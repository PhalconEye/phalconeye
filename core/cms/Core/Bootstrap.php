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

namespace Core;

use Core\Model\LanguageModel;
use Core\Model\LanguageTranslationModel;
use Core\Model\SettingsModel;
use Core\Plugin\DispatchPlugin;
use Engine\AbstractBootstrap;
use Engine\Behavior\DIBehavior;
use Engine\Config;
use Engine\Translation\DatabaseTranslations as TranslationDb;
use Phalcon\DI;
use Phalcon\Events\Manager;
use Phalcon\Translate\Adapter\NativeArray as TranslateArray;
use User\Model\UserModel;

/**
 * Core Bootstrap.
 *
 * @category  PhalconEye
 * @package   Core
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * Current module name.
     *
     * @var string
     */
    protected $_moduleName = __NAMESPACE__;

    /**
     * Bootstrap construction.
     *
     * @param DIBehavior|DI $di Dependency injection.
     * @param Manager       $em Events manager object.
     */
    public function __construct($di, $em)
    {
        parent::__construct($di, $em);

        /**
         * Attach this bootstrap for all application initialization events.
         */
        $em->attach('init', $this);
    }

    /**
     * Init some subsystems after engine initialization.
     */
    public function afterEngine()
    {
        $di = $this->getDI();
        if (!$di->getRegistry()->initialized) {
            return;
        }

        // Initialize i18n.
        $coreApi = $di->get('Core');
        $coreApi->i18n()->init();

        // Remove profiler for non-user.
        if (!UserModel::getViewer()->id) {
            $di->remove('profiler');
        }

        /**
         * Listening to events in the dispatcher using the Acl.
         */
        $eventsManager = $this->getEventsManager();
        $eventsManager->attach('dispatch', $coreApi->acl());
        $eventsManager->attach("dispatch", new DispatchPlugin());

        /**
         * Install assets if required.
         */
        if ($this->getConfig()->application->debug) {
            $di->getAssets()->installAssets();
        }
    }
}