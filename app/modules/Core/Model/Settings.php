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

namespace Core\Model;

use Engine\Db\AbstractModel;

/**
 * Settings.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("settings")
 */
class Settings extends AbstractModel
{
    const
        /**
         * Cache prefix.
         */
        CACHE_PREFIX = 'setting_';

    /**
     * @Primary
     * @Identity
     * @Column(type="string", nullable=false, column="name", size="60")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="value", size="250")
     */
    public $value;

    /**
     * Set value of current setting and save to db.
     *
     * @param mixed $value Setting value.
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->save();

        // Clear cache.
        $this->getDI()->get('cacheData')->delete(self::CACHE_PREFIX . $this->name);
    }

    /**
     * Get setting by name.
     *
     * @param string     $name    Setting name.
     * @param null|mixed $default Default value.
     *
     * @return null|string
     */
    public static function getSetting($name, $default = null)
    {
        $setting = self::getSettingObject($name);
        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Get setting object by name.
     *
     * @param string $name Setting name.
     *
     * @return null|Settings
     */
    public static function getSettingObject($name)
    {
        return Settings::findFirst(
            [
                'name = :name:',
                'bind' => [
                    'name' => $name
                ],
                'cache' => [
                    'key' => self::CACHE_PREFIX . $name
                ]
            ]
        );
    }

    /**
     * Set setting by name.
     *
     * @param string $name  Setting name.
     * @param mixed  $value Setting value.
     */
    public static function setSetting($name, $value)
    {
        $setting = self::getSettingObject($name);

        if (!$setting) {
            $setting = new Settings();
            $setting->name = $name;
        }

        $setting->setValue($value);
    }

    /**
     * Set array settings with key related values.
     *
     * @param array $settings Settings data (key=>value).
     *
     * @return void
     */
    public static function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            self::setSetting($key, $value);
        }
    }
}
