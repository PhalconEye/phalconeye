<?php

namespace Main\Form\Backoffice;

use Core\Form\CoreForm;

/**
 * Created by PhpStorm.
 * User: lantian
 * Date: 01.11.16
 * Time: 0:03
 */
class ConfigForm extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Main Settings')
            ->setDescription('Module settings example');

        $this->addContentFieldSet()
            ->addText('example_setting', 'Example setting');

        $this->addFooterFieldSet()->addButton('save');
    }
}