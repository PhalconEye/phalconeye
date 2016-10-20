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

namespace Core\Helper;

use Core\Model\SettingsModel;
use Engine\Helper\AbstractHelper;

/**
 * System settings helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class SettingHelper extends AbstractHelper
{
    const
        /**
         * System setting prefix
         */
        PREFIX = 'system';

    /**
     * Get setting by name.
     *
     * @param string     $setting Setting name.
     * @param null|mixed $default Default value.
     *
     * @return null|string
     */
    public function get($setting, $default = null)
    {
        return SettingsModel::getValue(static::PREFIX, $setting, $default);
    }
}