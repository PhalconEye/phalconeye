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

class Form_Admin_Menus_CreateItem extends Form
{

    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new MenuItem();
        }

        $model->prepareRoles();
        $model->prepareLanguages();

        parent::__construct($model);

    }

    public function init()
    {
        $this
            ->setOption('description', "This menu item will be available under menu or parent menu item.");


        $this->addElement('text', 'title', array(
            'label' => 'Title',
        ));


        $this->addElement('select', 'target', array(
            'label' => 'Title',
            'description' => 'Link type',
            'options' => array(
                null => 'Default link',
                '_blank' => 'Opens the linked document in a new window or tab',
                '_parent' => 'Opens the linked document in the parent frame',
                '_top' => 'Opens the linked document in the full body of the window',
            )
        ));

        $this->addElement('radio', 'url_type', array(
            'label' => 'Select url type',
            'options' => array(
                0 => 'Url',
                1 => 'System page'
            ),
            'value' => 0
        ));

        $this->addElement('text', 'url', array(
            'label' => 'Url',
        ));

        $this->addElement('text', 'page', array(
            'label' => 'Page',
            'description' => 'Start typing to see pages variants.',
            'data-link' => '/admin/pages/suggest',
            'data-target' => '#page_id',
            'autocomplete' => 'off',
            'class' => 'autocomplete',
        ));

        $this->addElement('textArea', 'onclick', array(
            'label' => 'Onclick',
            'description' => 'Type JS action that will be performed when this menu item is selected.'
        ));

        $this->addElement('textArea', 'tooltip', array(
            'label' => 'Tooltip'
        ));

        $this->addElement('html', 'ckeditor',
            array(
                'ignore' => true,
                'html' => '<script type="text/javascript">$(document).ready(setTimeout(function () {CKEDITOR.replace("tooltip");}, 300));</script>'
            ), 1000);

        $this->addElement('select', 'tooltip_position', array(
            'label' => 'Tooltip position',
            'options' => array(
                'top' => 'Top',
                'bottom' => 'Bottom',
                'left' => 'Left',
                'right' => 'Right'
            )
        ));

        $this->addElement('remoteFile', 'icon', array(
            'label' => 'Icon',
            'title' => $this->di->get('trans')->_('Select file')
        ));

        $this->addElement('select', 'icon_position', array(
            'label' => 'Icon position',
            'options' => array(
                'left' => 'Left',
                'right' => 'Right'
            )
        ));

        $this->addElement('select', 'languages', array(
            'label' => 'Languages',
            'description' => 'Choose the language in which the menu item will be displayed. If no one selected - will be displayed at all.',
            'options' => Language::find(),
            'using' => array('locale', 'name'),
            'multiple' => 'multiple'
        ));

        $this->addElement('select', 'roles', array(
            'label' => 'Roles',
            'description' => 'If no value is selected, will be allowed to all (also as all selected).',
            'options' => Role::find(),
            'using' => array('id', 'name'),
            'multiple' => 'multiple'
        ));


        $this->addElement('hidden', 'page_id');
        $this->addElement('hidden', 'menu_id');
        $this->addElement('hidden', 'parent_id');

    }
}