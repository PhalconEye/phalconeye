<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

use Core\Model\Language;
use Core\Model\LanguageTranslation;
use Core\Model\Settings;
use Core\Model\Widget;
use Engine\Bootstrap as EngineBootstrap;
use Engine\Cache\System;
use Engine\Config;
use Engine\Translation\Db as TranslationDb;
use Phalcon\DI;
use Phalcon\DiInterface;
use Phalcon\Events\Manager;
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
 * @copyright 2013-2014 PhalconEye Team
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
     * @param DiInterface $di Dependency injection.
     * @param Manager     $em Events manager object.
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

        $this->_initI18n($di, $config);
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
     * Init locale.
     *
     * @param DI     $di     Dependency injection.
     * @param Config $config Dependency injection.
     *
     * @return void
     */
    protected function _initI18n(DI $di, Config $config)
    {
        if ($di->get('app')->isConsole()) {
            return;
        }

        $languageObject = null;
        if (!$di->get('session')->has('language')) {
            /** @var Language $languageObject */
            if ($config->installed) {
                $language = Settings::getSetting('system_default_language');
                if ($language == 'auto') {
                    $locale = \Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
                    $languageObject = Language::findFirst("language = '" . $locale . "' OR locale = '" . $locale . "'");
                } else {
                    $languageObject = Language::findFirst("language = '" . $language . "'");
                }
            }

            if ($languageObject) {
                $di->get('session')->set('language', $languageObject->language);
                $di->get('session')->set('locale', $languageObject->locale);
            } else {
                $di->get('session')->set('language', Config::CONFIG_DEFAULT_LANGUAGE);
                $di->get('session')->set('locale', Config::CONFIG_DEFAULT_LOCALE);
            }
        }

        $language = $di->get('session')->get('language');
        $translate = null;

        if (!$config->application->debug || !$config->installed) {
            $messages = [];
            $directory = $config->application->languages->cacheDir;
            $extension = ".php";

            if (file_exists($directory . $language . $extension)) {
                require $directory . $language . $extension;
            } else {
                if (file_exists($directory . Config::CONFIG_DEFAULT_LANGUAGE . $extension)) {
                    // fallback to default
                    require $directory . Config::CONFIG_DEFAULT_LANGUAGE . $extension;
                }
            }

            $translate = new TranslateArray(
                [
                    "content" => $messages
                ]
            );
        } else {
            if (!$languageObject) {
                $languageObject = Language::findFirst(
                    [
                        'conditions' => 'language = :language:',
                        'bind' => (
                            [
                                "language" => $language
                            ]
                            )
                    ]
                );

                if (!$languageObject) {
                    $languageObject = Language::findFirst("language = '" . Config::CONFIG_DEFAULT_LANGUAGE . "'");
                }
            }

            $translate = new TranslationDb($di, $languageObject->getId(), new LanguageTranslation());
        }

        $di->set('i18n', $translate);
    }

    /**
     * Prepare widgets metadata for Engine.
     *
     * @param DI $di Dependency injection.
     *
     * @return void
     */
    protected function _initWidgets(DI $di)
    {
        if ($di->get('app')->isConsole()) {
            return;
        }

        $cache = $di->get('cacheData');
        $widgets = $cache->get(System::CACHE_KEY_WIDGETS_METADATA);

        if ($widgets === null) {
            $widgets = [];
            foreach (Widget::find() as $object) {
                $widgets[] = [$object->id, $object->getKey(), $object];
            }

            $cache->save(System::CACHE_KEY_WIDGETS_METADATA, $widgets, 0); // Unlimited.
        }
        $di->get('widgets')->addWidgets($widgets);
    }
}