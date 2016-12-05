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

namespace Core\Api;

use Core\Model\LanguageModel;
use Core\Model\LanguageTranslationModel;
use Core\Model\SettingsModel;
use Engine\Api\AbstractApi;
use Engine\Config;
use Engine\Translation\DatabaseTranslations;
use Phalcon\Translate\Adapter\NativeArray;

/**
 * Auth api.
 *
 * @category  PhalconEye
 * @package   Core\Api
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class I18nApi extends AbstractApi
{
    const
        AUTO_DETECT_LANGUAGE = 'auto';

    const
        SESSION_LANGUAGE = 'language',
        SESSION_LOCALE = 'locale';

    const
        SETTING_SCOPE = 'system',
        SETTING_DEFAULT_LANGUAGE = 'default_language';

    /**
     * Initialize I18n API.
     * Detect and/or setup language in session.
     * Setup API into DI.
     */
    public function init()
    {
        if ($this->getApp()->isConsole()) {
            return;
        }

        $language = null;
        if (!$this->getSession()->has(self::SESSION_LANGUAGE)) {
            $defaultLanguage = $this->getDefaultLanguage();
            if ($defaultLanguage == self::AUTO_DETECT_LANGUAGE) {
                $language = $this->detectLanguage();
            } else {
                $language = $this->findLanguage($defaultLanguage);
            }

            if ($language) {
                $this->setLanguage($language);
            } else {
                $this->setLanguage(Config::CONFIG_DEFAULT_LANGUAGE);
            }
        }

        $language = $this->getLanguage();
        $this->_setStorage($language);
        $this->_setBaseUrl($language);
    }

    /**
     * Authenticate user.
     *
     * @param LanguageModel|string $language Language string or object.
     *
     * @return void
     */
    public function setLanguage($language)
    {
        if (is_string($language)) {
            $language = $this->findLanguage($language);
        }

        if (empty($language)) {
            return;
        }

        $this->getSession()->set(self::SESSION_LANGUAGE, $language->language);
        $this->getSession()->set(self::SESSION_LOCALE, $language->locale);
        $this->_setStorage($language->language);
        $this->_setBaseUrl($language->language);
    }

    /**
     * Get current language.
     *
     * @return string
     */
    public function getLanguage():string
    {
        if (!$this->getSession()->has(self::SESSION_LANGUAGE)) {
            return Config::CONFIG_DEFAULT_LANGUAGE;
        }

        return $this->getSession()->get(self::SESSION_LANGUAGE);
    }

    /**
     * Set current locale.
     *
     * @param string $locale Locale to setup in session.
     */
    public function setLocale(string $locale)
    {
        $this->getSession()->set(self::SESSION_LOCALE, $locale);
    }

    /**
     * Get current locale.
     *
     * @return string
     */
    public function getLocale():string
    {
        if (!$this->getSession()->has(self::SESSION_LOCALE)) {
            return Config::CONFIG_DEFAULT_LOCALE;
        }

        return $this->getSession()->get(self::SESSION_LOCALE);
    }

    /**
     * Get system default language.
     *
     * @return string
     */
    public function getDefaultLanguage() : string
    {
        return SettingsModel::getValue(self::SETTING_SCOPE, self::SETTING_DEFAULT_LANGUAGE);
    }

    /**
     * Find language object.
     *
     * @param string $language Language name (en, ru, etc).
     * @param string $locale   Locale name (en_EN, ru_RU, etc).
     *
     * @return LanguageModel|boolean
     */
    public function findLanguage(string $language, string $locale = null)
    {
        if (empty($locale)) {
            $locale = $language;
        }

        return LanguageModel::findFirst(['language = ?0 OR locale = ?1', 'bind' => [$language, $locale]]);
    }

    /**
     * Try to detect language from context.
     *
     * @return LanguageModel
     */
    public function detectLanguage() :LanguageModel
    {
        $locale = \Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        return $this->findLanguage($locale);
    }

    /**
     * Set language translation storage depending on language.
     *
     * @param string $language Language name.
     */
    protected function _setStorage(string $language)
    {
        $translate = null;

        if (!$this->getConfig()->application->debug) {
            $translate = $this->_loadFromFiles($language);
        } else {
            $translate = $this->_loadFromDatabase($language);
        }

        $this->getDI()->setShared('i18n', $translate);
    }

    /**
     * Set language in base url.
     *
     * @param string $language Language name.
     */
    protected function _setBaseUrl(string $language)
    {
        $config = $this->getDI()->getConfig();
        if ($config->core->languages->languageInUrl) {
            $this->getUrl()->setBaseUri($config->application->baseUrl . $language . '/');
        }
    }

    /**
     * Load translations from database.
     *
     * @param string $language Language name.
     *
     * @return DatabaseTranslations
     */
    protected function _loadFromDatabase(string $language) : DatabaseTranslations
    {
        $language = $this->findLanguage($language);
        if (!$language) {
            $language = $this->findLanguage($this->getDefaultLanguage());
        }
        return new DatabaseTranslations($this->getDI(), $language->getId(), new LanguageTranslationModel());
    }

    /**
     * Load translations from stored php files.
     *
     * @param string $language Language name.
     *
     * @return NativeArray
     */
    protected function _loadFromFiles(string $language) : NativeArray
    {
        $messages = [];
        $directory = $this->getConfig()->core->languages->cacheDir;
        $extension = ".php";
        $languageFile = '';

        if (file_exists($directory . $language . $extension)) {
            $languageFile = $directory . $language . $extension;
        } elseif (file_exists($directory . Config::CONFIG_DEFAULT_LANGUAGE . $extension)) {
            $languageFile = $directory . Config::CONFIG_DEFAULT_LANGUAGE . $extension;
        }

        if (!empty($languageFile)) {
            require $languageFile;
        }

        return new NativeArray(["content" => $messages]);
    }
}