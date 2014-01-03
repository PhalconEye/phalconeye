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
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core;

use Core\Model\Settings;
use Core\Model\Widget;
use Engine\Bootstrap as EngineBootstrap;
use Engine\EventsManager;
use Engine\Translation\Db as TranslationDb;
use Engine\Widget\Storage;
use Phalcon\Config;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use Phalcon\Translate\Adapter\NativeArray as TranslateArray;
use User\Model\User;

/**
 * Core Bootstrap.
 *
 * @category  PhalconEye
 * @package   Core
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Bootstrap extends EngineBootstrap
{
    /**
     * Current module name.
     *
     * @var string
     */
    protected $_moduleName = "Core";

    /**
     * Bootstrap construction.
     *
     * @param DiInterface   $di Dependency injection.
     * @param EventsManager $em Events manager object.
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
        $config = $this->getConfig();

        $this->_initLocale($di, $config);
        if (!$config->installed) {
            return;
        }

        // Remove profiler for non-user.
        if (!User::getViewer()->id) {
            $di->remove('profiler');
        }

        // Init widgets system.
        $this->_initWidgets($di);

        /**
         * Listening to events in the dispatcher using the Acl.
         */
        if ($config->installed) {
            $this->getEventsManager()->attach('dispatch', $di->get('core')->acl());
        }

        // Install assets if required.
        if ($config->application->debug) {
            $di->get('assets')->installAssets(PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme'));
        }
    }

    /**
     * Prepare widgets metadata for Engine.
     *
     * @param DI $di Dependency injection.
     *
     * @return void
     */
    private function _initWidgets(DI $di)
    {
        $cache = $di->get('cacheData');
        $cacheKey = "widgets_metadata.cache";
        $widgets = $cache->get($cacheKey);

        if ($widgets === null) {
            $widgetObjects = Widget::find();
            $widgets = [];
            foreach ($widgetObjects as $object) {
                $widgets[$object->id] = $object;
            }

            $cache->save($cacheKey, $widgets, 2592000); // 30 days.
        }
        Storage::setWidgets($widgets);
    }

    /**
     * Init locale.
     *
     * @param DI     $di     Dependency injection.
     * @param Config $config Dependency injection.
     *
     * @return void
     */
    private function _initLocale(DI $di, Config $config)
    {
        if ($config->installed) {
            $locale = $di->get('session')->get('locale', Settings::getSetting('system_default_language'));
        } else {
            $locale = $di->get('session')->get('locale', 'en');
        }

        $translate = null;

        if (!$di->get('config')->application->debug || !$config->installed) {
            $messages = [];
            if (file_exists(ROOT_PATH . "/app/var/languages/" . $locale . ".php")) {
                require ROOT_PATH . "/app/var/languages/" . $locale . ".php";
            } else {
                if (file_exists(ROOT_PATH . "/app/var/languages/en.php")) {
                    // fallback to default
                    require ROOT_PATH . "/app/var/languages/en.php";
                }
            }

            $translate = new TranslateArray(
                [
                    "content" => $messages
                ]
            );
        } else {
            $translate = new TranslationDb(
                [
                    'db' => $di->get('db'),
                    'locale' => $locale,
                    'model' => 'Core\Model\Language',
                    'translationModel' => 'Core\Model\LanguageTranslation'
                ]
            );
        }

        $di->set('trans', $translate);
    }
}