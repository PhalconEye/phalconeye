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
use Phalcon\Mvc\Model\ResultsetInterface;

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
        CACHE_PREFIX = 'setting_',

        /**
         * Setting separator
         */
        SEPARATOR = '_';

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
     * Save settings and clear cache
     *
     * {@inheritdoc}
     */
    public function save($data=null, $whiteList=null)
    {
        if (strlen($this->value) && !isset($data['value'])) {
            parent::save($data, $whiteList) and $this->_clearCache();
        } else {
            $this->delete();
        }
        return true;
    }

    /**
     * Delete a setting and clear cache
     *
     * {@inheritdoc}
     */
    public function delete()
    {
        parent::delete() and $this->_clearCache();
        return true;
    }

    /**
     * Update cache with new value
     */
    protected function _updateCache()
    {
        $this->getDI()->get('cacheData')->save(self::CACHE_PREFIX . $this->name, $this->value);
    }

    /**
     * Delete setting from cache
     */
    protected function _clearCache()
    {
        $this->getDI()->get('cacheData')->delete(self::CACHE_PREFIX . $this->name);
    }

    /**
     * Get module's setting or a list
     *
     * @param string      $module  Module name.
     * @param null|string $setting Setting name.
     * @param null|mixed  $default Default value.
     *
     * @return mixed
     */
    public static function getValue($module, $setting = null, $default = null)
    {
        $settingObject = self::factory($module, $setting);

        if (!$settingObject) {
            return $default;
        }

        if ($settingObject instanceof ResultsetInterface) {
            $rows = [];
            /** @var $entity Settings **/
            foreach ($settingObject as $entity) {
                $entityName = substr($entity->name, strlen($module . self::SEPARATOR));
                $rows[$entityName] = $entity->value;
                $entity->_updateCache();
            }
            return $rows;
        }

        return $settingObject->value;
    }

    /**
     * Set setting by name.
     *
     * @param string      $module  Module name.
     * @param null|string $setting Setting name.
     * @param mixed       $value   Setting value.
     *
     * @throw \InvalidArgumentException
     */
    public static function setValue($module, $setting, $value)
    {
        if (empty($setting)) {
            throw new \InvalidArgumentException('Missing required $setting');
        }

        $settingObject = self::factory($module, $setting);
        if (!$settingObject) {
            $settingObject = new self;
        }
        $settingObject->name = $module . self::SEPARATOR . $setting;
        $settingObject->value = $value;
        $settingObject->save();
    }

    /**
     * Create module's setting instance or a resultset
     *
     * @param string      $module  Module name.
     * @param null|string $setting Setting name.
     *
     * @return null|Settings|Settings[]
     */
    public static function factory($module, $setting = null)
    {
        if (empty($setting)) {
            return self::find(
                [
                    'name LIKE :name:',
                    'bind' => [
                        'name' => $module . self::SEPARATOR .'%'
                    ]
                ]
            );
        } else {
            return self::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => $module . self::SEPARATOR . $setting
                    ],
                    'cache' => [
                        'key' => self::CACHE_PREFIX . $module . self::SEPARATOR . $setting
                    ]
                ]
            );
        }
    }

    /**
     * Get module's setting using full name
     *
     * @param null|string $name    Setting name.
     * @param null|mixed  $default Default value.
     *
     * @deprecated since 0.5, use Settings::getValue() instead
     * @return mixed
     */
    public static function getSetting($name, $default = null)
    {
        return self::getValue(
            strstr($name, self::SEPARATOR, true),
            ltrim(strstr($name, self::SEPARATOR), self::SEPARATOR),
            $default
        );
    }

    /**
     * Set module's setting using full name
     *
     * @param null|string $name  Setting name.
     * @param null|mixed  $value Setting value.
     *
     * @deprecated since 0.5, use Settings::setValue() instead
     * @return mixed
     */
    public static function setSetting($name, $value)
    {
        self::setValue(
            strstr($name, self::SEPARATOR, true),
            ltrim(strstr($name, self::SEPARATOR), self::SEPARATOR),
            $value
        );
    }
}
