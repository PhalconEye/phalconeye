<?php

class Form_Admin_Menus_CreateItem extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('menu_id')
            ->addIgnored('page_id')
            ->addIgnored('parent_id')
            ->addIgnored('item_order')
            ->addIgnored('languages')
        ;

        if ($model === null) {
            $model = new MenuItem();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('description', "This menu item will be available under menu or parent menu item.");

        $targetOptions = array(
            null => 'Default link',
            '_blank' => 'Opens the linked document in a new window or tab',
            '_parent' => 'Opens the linked document in the parent frame',
            '_top' => 'Opens the linked document in the full body of the window',
        );

        $this->setElementParam('target', 'options', $targetOptions);
        $this->setElementParam('target', 'description', 'Link type');
        $this->setElementAttrib('target', 'order', 1);


        $this->addElement('radioField', 'url_type', array(
            'label' => 'Select url type',
            'options' => array(
                0 => 'Url',
                1 => 'System page'
            ),
            'value' => 0
        ),2);

        $this->addElement('hiddenField', 'page_id');
        $this->addElement('textField', 'page', array(
            'label' => 'Page',
            'description' => 'Start typing to see pages variants',
            'data-link' => '/admin/pages/suggest',
            'data-target' => '#page_id',
            'autocomplete' => 'off',
            'class' => 'autocomplete',
        ));


        $this->addElement('hiddenField', 'menu_id');
        $this->addElement('hiddenField', 'parent_id');
    }
}