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

class Settings extends \Phalcon\Mvc\Model
{

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $value;


    /**
     * Method to set the value of field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Method to set the value of field value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->save();
    }


    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getSource()
    {
        return "settings";
    }

    /**
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

        return $setting->getValue();
    }

    /**
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
                )
            ));


        return $setting;
    }

    public static function setSetting($name, $value)
    {
        $setting = self::getSettingObject($name);

        if (!$setting) {
            $setting = new Settings();
            $setting->setName($name);
        }

        $setting->setValue($value);
    }

    public static function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            self::setSetting($key, $value);
        }
    }

}
