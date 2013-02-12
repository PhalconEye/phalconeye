<?php

class Form_Admin_Languages_CreateItem extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('language_id')
        ;

        if ($model === null) {
            $model = new LanguageTranslation();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this->addElement('hiddenField', 'language_id');
    }
}