<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Backoffice\Package;

use Core\Model\PackageModel;
use Engine\Form\Validator\RegexValidator;
use Engine\Package\Manager;

/**
 * Edit package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageEditForm extends PackageCreateForm
{
    /**
     * Back link.
     *
     * @var string
     */
    protected $_link;

    /**
     * Create form.
     *
     * @param PackageModel $entity Entity object.
     * @param string       $link   Back link.
     */
    public function __construct(PackageModel $entity = null, $link = 'backoffice-packages')
    {
        $this->_link = $link;
        parent::__construct();

        if (!$entity) {
            $entity = new PackageModel();
        }

        $this->addEntity($entity);
        if ($entity->type == Manager::PACKAGE_TYPE_WIDGET) {
            $widget = $entity->getWidget();
            if ($widget->admin_form && $widget->admin_form != 'action') {
                $this->setValue('form_class', $widget->admin_form);
                $widget->admin_form = 'form_class';
            }
            $this->addEntity($widget, 'widget');
        }
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this
            ->setTitle('Edit Package')
            ->setDescription('Edit this package.');

        $this->getFieldSet(self::FIELDSET_CONTENT)
            ->remove('name')
            ->remove('type')
            ->remove('header')
            ->addHidden('type')
            ->addHidden('name');

        $this->getFieldSet(self::FIELDSET_FOOTER)
            ->clearElements()
            ->addButton('save')
            ->addButtonLink('cancel', 'Cancel', ['for' => $this->_link]);

        $this->getFieldSet(self::FIELDSET_WIDGET)
            ->remove('module')
            ->addHidden('module');
    }

    /**
     * Set elements validation.
     *
     * @return void
     */
    protected function _setValidation()
    {
        $fieldSet = $this->getFieldSet(self::FIELDSET_CONTENT);
        $fieldSet->getValidation()
            ->add(
                'version',
                new RegexValidator(
                    [
                        'pattern' => '/\d+(\.\d+)+/',
                        'message' => 'Version must be in correct format: 1.0.0 or 1.0.0.0'
                    ]
                )
            );
    }
}