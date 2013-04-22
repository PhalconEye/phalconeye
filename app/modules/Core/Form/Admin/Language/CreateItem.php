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

namespace Core\Form\Admin\Language;

class CreateItem extends \Engine\Form
{

    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new \Core\Model\LanguageTranslation();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this->addElement('textArea', 'original', array(
            'label' => 'Original'
        ));

        $this->addElement('textArea', 'translated', array(
            'label' => 'Translated'
        ));

        $this->addElement('hidden', 'language_id');
    }
}