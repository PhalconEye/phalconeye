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

namespace Engine;

use Phalcon\Config as PhalconConfig;

/**
 * Application config.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Config extends PhalconConfig
{
    const
        /**
         * System config location.
         */
        CONFIG_PATH = '/app/config/',

        /**
         * System config location.
         */
        CONFIG_CACHE_PATH = '/app/var/cache/data/config.php',

        /**
         * Default language if there is no default selected.
         */
        CONFIG_DEFAULT_LANGUAGE = 'en',

        /**
         * Default locale if there no default language selected.
         */
        CONFIG_DEFAULT_LOCALE = 'en_US',

        /**
         * Application metadata.
         */
        CONFIG_METADATA_APP = '/app/var/data/app.php',

        /**
         * Packages metadata location.
         */
        CONFIG_METADATA_PACKAGES = '/app/var/data/packages',

        /**
         * Default configuration section.
         */
        CONFIG_DEFAULT_SECTION = 'application';

    /**
     * Current config stage.
     *
     * @var string
     */
    private $_currentStage;

    /**
     * Create configuration object.
     *
     * @param array|null  $arrayConfig Configuration data.
     * @param string|null $stage       Configuration stage.
     */
    public function __construct($arrayConfig = null, $stage = null)
    {
        $this->_currentStage = $stage;
        parent::__construct($arrayConfig);
    }

    /**
     * Load configuration according to selected stage.
     *
     * @param string $stage Configuration stage.
     *
     * @return Config
     */
    public static function factory($stage = null)
    {
        if (!$stage) {
            $stage = APPLICATION_STAGE;
        }

        if ($stage == APPLICATION_STAGE_DEVELOPMENT) {
            $config = self::_getConfiguration($stage);
        } else {
            if (file_exists(self::CONFIG_CACHE_PATH)) {
                $config = new Config(include_once(self::CONFIG_CACHE_PATH), $stage);
            } else {
                $config = self::_getConfiguration($stage);
                $config->refreshCache();
            }
        }

        return $config;
    }

    /**
     * Save config file into cached config file.
     *
     * @return void
     */
    public function refreshCache()
    {
        file_put_contents(ROOT_PATH . self::CONFIG_CACHE_PATH, $this->_toConfigurationString());
    }

    /**
     * Save config.
     *
     * @param string|array $sections Config section name to save. By default: Config::CONFIG_DEFAULT_SECTION.
     *
     * @return void
     */
    public function save($sections = self::CONFIG_DEFAULT_SECTION)
    {
        if (!$this->_currentStage) {
            return;
        }

        $configDirectory = ROOT_PATH . self::CONFIG_PATH . $this->_currentStage;
        if (!is_array($sections)) {
            $sections = array($sections);
        }

        foreach ($sections as $section) {
            file_put_contents(
                $configDirectory . '/' . $section . '.php',
                $this->_toConfigurationString($this->get($section)->toArray())
            );
        }
        $this->refreshCache();
    }

    /**
     * Load configuration from all files.
     *
     * @param string $stage Configuration stage.
     *
     * @throws Exception
     * @return Config
     */
    protected static function _getConfiguration($stage)
    {
        $config = new Config(null, $stage);
        $configDirectory = ROOT_PATH . self::CONFIG_PATH . $stage;
        foreach (scandir($configDirectory) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $data = include_once($configDirectory . '/' . $file);
            $config->offsetSet(basename($file, ".php"), $data);
        }

        $appPath = ROOT_PATH . self::CONFIG_METADATA_APP;

        if (!file_exists($appPath)) {
            $config->offsetSet('installed', false);
            $config->offsetSet('events', array());
            $config->offsetSet('modules', array());
            return $config;
        }

        $data = include_once($appPath);
        $config->merge(new Config($data));

        return $config;
    }

    /**
     * Save application config to file.
     *
     * @param array|null $data Configuration data.
     *
     * @return void
     */
    protected function _toConfigurationString($data = null)
    {
        if (!$data) {
            $data = $this->toArray();
        }
        $configText = var_export($data, true);

        // Fix pathes. This related to windows directory separator.
        $configText = str_replace('\\\\', DS, $configText);

        $configText = str_replace("'" . PUBLIC_PATH, "PUBLIC_PATH . '", $configText);
        $configText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $configText);
        $headerText = '<?php
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
*/

/**
* WARNING
*
* Manual changes to this file may cause a malfunction of the system.
* Be careful when changing settings!
*
*/

return ';

        return $headerText . $configText . ';';
    }
}