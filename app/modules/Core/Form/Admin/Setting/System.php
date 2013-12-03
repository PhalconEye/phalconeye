<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Core\Form\Admin\Setting;

use Core\Model\Language;
use Core\Model\Settings;
use Engine\Form;

/**
 * System settings.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Setting
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class System extends Form
{
    const
        /**
         * Themes directory location.
         */
        THEMES_DIR = '/themes/';

    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('title', "System settings")
            ->setOption('description', "All system settings here.");

        $this->addElement('text', 'system_title', array(
            'label' => 'Site name',
            'value' => Settings::getSetting('system_title', '')
        ));

        $themes = array();

        foreach (scandir(PUBLIC_PATH . self::THEMES_DIR) as $entry) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $themes[$entry] = ucfirst($entry);
        }

        $this->addElement('select', 'system_theme', array(
            'label' => 'Theme',
            'options' => $themes,
            'value' => Settings::getSetting('system_theme')
        ));

        $this->addElement('select', 'system_default_language', array(
            'label' => 'Default language',
            'options' => Language::find(),
            'using' => array('locale', 'name'),
            'value' => Settings::getSetting('system_default_language')
        ));

        $this->addButton('Save', true);
    }
}