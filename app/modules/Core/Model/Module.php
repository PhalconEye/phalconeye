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

class Module extends \Phalcon\Mvc\Model
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
    protected $title;

    /**
     * @var string
     *
     */
    protected $description;

    /**
     * @var string
     *
     */
    protected $version;

    /**
     * @var integer
     *
     */
    protected $enabled;


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
     * Method to set the value of field title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Method to set the value of field version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
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
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the value of field version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }


    public function getSource()
    {
        return "modules";
    }

    public static function getEnabledModules($cache = true){
        if ($cache) {
            /** @var \Phalcon\Cache\Backend\Memcache $cacheData */
            $cacheData = \Phalcon\DI::getDefault()->get('cacheData');
            $cacheKey = "modules.cache";

            $modules = $cacheData->get($cacheKey);

            if ($modules === null) {
                $modules = self::find();

                $cacheData->save($cacheKey, $modules);
            }

            return $modules;
        }

        return self::find();
    }
}
