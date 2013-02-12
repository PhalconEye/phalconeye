<?php


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
     * @return null|Settings
     */
    public static function getSetting($name, $default = null)
    {
        $setting = self::getSettingObject($name);
        if (!$setting) {
            return $default;
        }

        return $setting->getValue();
    }

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
