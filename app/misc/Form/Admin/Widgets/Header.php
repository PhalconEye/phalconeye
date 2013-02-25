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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

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