<?php
class Form_Admin_Settings_System extends Form
{
    CONST THEMES_DIR = '/public/themes/';

    public function init()
    {
        $this
            ->setOption('title', "System settings")
            ->setOption('description', "All system settings here.");


        $this->addElement('textField', 'system_title', array(
            'label' => 'Site name',
            'value' => Settings::getSetting('system_title', '')
        ));

        $themes = array();

        foreach (scandir(ROOT_PATH . self::THEMES_DIR) as $entry) {
            if ($entry == '.' || $entry == '..') continue;
            $themes[$entry] = ucfirst($entry);
        }

        $this->addElement('selectStatic', 'system_theme', array(
            'label' => 'Theme',
            'options' => $themes,
            'value' => Settings::getSetting('system_theme')
        ));

        $this->addElement('selectStatic', 'system_default_language', array(
            'label' => 'Default language',
            'options' => Language::find(),
            'using' => array('locale', 'name'),
            'value' => Settings::getSetting('system_default_language')
        ));

        $this->addButton('Save', true);
    }
}