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

namespace Core\Form\Admin\Setting;

class System extends \Engine\Form
{
    CONST THEMES_DIR = '/public/themes/';

    public function init()
    {
        $this
            ->setOption('title', "System settings")
            ->setOption('description', "All system settings here.");

        $this->addElement('text', 'system_title', array(
            'label' => 'Site name',
            'value' => \Core\Model\Settings::getSetting('system_title', '')
        ));

        $themes = array();

        foreach (scandir(ROOT_PATH . self::THEMES_DIR) as $entry) {
            if ($entry == '.' || $entry == '..') continue;
            $themes[$entry] = ucfirst($entry);
        }

        $this->addElement('select', 'system_theme', array(
            'label' => 'Theme',
            'options' => $themes,
            'value' => \Core\Model\Settings::getSetting('system_theme')
        ));

        $this->addElement('select', 'system_default_language', array(
            'label' => 'Default language',
            'options' => \Core\Model\Language::find(),
            'using' => array('locale', 'name'),
            'value' => \Core\Model\Settings::getSetting('system_default_language')
        ));

        $this->addButton('Save', true);

    }
}