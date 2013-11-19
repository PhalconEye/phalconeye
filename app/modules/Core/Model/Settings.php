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

namespace Core\Model;

/**
 * @Source("settings")
 */
class Settings extends \Engine\Db\Model
{
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
     * Set value of current setting and save to db
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->save();

        // clear cache
        $this->getDI()->get('cacheData')->delete('setting_' . $this->name . '.cache');
    }

    /**
     * Get setting by name
     *
     * @param $name
     * @param null $default
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
     * Get setting object by name
     *
     * @param $name
     * @return null|Settings
     */
    public static function getSettingObject($name)
    {
        $setting = Settings::findFirst(
            array(
                'name = :name:',
                'bind' => array(
                    'name' => $name
                ),
                'cache' => array(
                    'key' => 'setting_' . $name . '.cache'
                )
            ));


        return $setting;
    }

    /**
     * Set setting by name
     *
     * @param $name
     * @param $value
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
     * Set array settings with key related values
     *
     * @param array $settings
     */
    public static function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            self::setSetting($key, $value);
        }
    }

}
