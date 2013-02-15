<?php

class Form_Admin_Widgets_Header extends Form
{

    public function init()
    {
        $this
            ->setOption('description', "Settings for header of you site.");

        $this->addElement('textField', 'logo', array(
            'label' => 'Logo image (url)'
        ));

        $this->addElement('checkField', 'show_title', array(
            'label' => 'Show site title',
            'options' => 1
        ));

        $this->addElement('checkField', 'show_auth', array(
            'label' => 'Show authentication links (logo, register, logout, etc)',
            'options' => 1
        ));

    }
}