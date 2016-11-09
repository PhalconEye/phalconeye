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
use Engine\AbstractBootstrap;
use Engine\Behavior\DIBehavior;
use Engine\Config;
use Engine\Translation\Db as TranslationDb;
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

        $config = $this->getConfig();

        $this->_initI18n($di, $config);

        // Remove profiler for non-user.
        if (!UserModel::getViewer()->id) {
            $di->remove('profiler');
        }

        /**
         * Listening to events in the dispatcher using the Acl.
         */
        $this->getEventsManager()->attach('dispatch', $di->get('core')->acl());

        /**
         * Set current theme.
         */
        $assets = $di->getAssets();
        $assets->setTheme(SettingsModel::getValue('system', 'theme'));

        /**
         * Install assets if required.
         */
        if ($config->application->debug) {
            $assets->installAssets();
        }
    }

    /**
     * Init locale.
     *
     * @param DIBehavior|DI $di     Dependency injection.
     * @param Config        $config Dependency injection.
     *
     * @return void
     */
    protected function _initI18n($di, $config)
    {
        if ($di->getApp()->isConsole()) {
            return;
        }

        $languageObject = null;
        if (!$di->getSession()->has('language')) {
            /** @var LanguageModel $languageObject */
            $language = SettingsModel::getValue('system', 'default_language');
            if ($language == 'auto') {
                $locale = \Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
                $languageObject = LanguageModel::findFirst(
                    "language = '" . $locale . "' OR locale = '" . $locale . "'"
                );
            } else {
                $languageObject = LanguageModel::findFirst("language = '" . $language . "'");
            }

            if ($languageObject) {
                $di->getSession()->set('language', $languageObject->language);
                $di->getSession()->set('locale', $languageObject->locale);
            } else {
                $di->getSession()->set('language', Config::CONFIG_DEFAULT_LANGUAGE);
                $di->getSession()->set('locale', Config::CONFIG_DEFAULT_LOCALE);
            }
        }

        $language = $di->getSession()->get('language');
        $translate = null;

        if (!$config->application->debug) {
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
                $languageObject = LanguageModel::findFirst(
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
                    $languageObject = LanguageModel::findFirst("language = '" . Config::CONFIG_DEFAULT_LANGUAGE . "'");
                }
            }

            $translate = new TranslationDb($di, $languageObject->getId(), new LanguageTranslationModel());
        }

        $di->set('i18n', $translate);
    }
}