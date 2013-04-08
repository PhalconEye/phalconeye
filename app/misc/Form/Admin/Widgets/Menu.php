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

class Form_Admin_Widgets_Menu extends Form
{

    public function init()
    {
        $this
            ->setOption('description', "Select menu that will be rendered.");

        $this->addElement('text', 'title', array(
            'label' => 'Title'
        ));

        $this->addElement('text', 'class', array(
            'label' => 'Menu css class'
        ));

        $this->addElement('text', 'menu', array(
            'label' => 'Menu',
            'description' => 'Start typing to see menus variants',
            'data-link' => '/admin/menus/suggest',
            'data-target' => '#menu_id',
            'autocomplete' => 'off',
            'class' => 'autocomplete',
        ));


        $this->addElement('hidden', 'menu_id');
    }
}