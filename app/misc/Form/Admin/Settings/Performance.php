<?php
class Form_Admin_Settings_Performance extends Form
{

    public function init()
    {
        $this
            ->setOption('title', "Performance settings");


        $this->addElement('checkField', 'clear_cache', array(
            'label' => 'Clear cache',
            'options' => 1
        ));


        $this->addButton('Save', true);
    }
}