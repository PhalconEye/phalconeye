<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Admin\Page;

use Core\Form\CoreForm;
use Core\Model\Page;
use Engine\Db\AbstractModel;
use Engine\Form\Validator\Regex;
use User\Model\Role;

/**
 * Create page.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Page
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Create extends CoreForm
{
    /**
     * @var Page
     */
    protected $_currentPageObject;

    /**
     * Create form.
     *
     * @param AbstractModel $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        $this->_currentPageObject = $entity;
        parent::__construct();

        if (!$entity) {
            $entity = new Page();
        }

        $this->addEntity($entity);
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Page Creation')
            ->setDescription('Create new page.');

        $content = $this->addContentFieldSet()
            ->addText('title')
            ->addText(
                'url',
                'Url',
                'Page will be available under http://' . $_SERVER['HTTP_HOST'] . '/page/[URL NAME]'
            )
            ->addTextArea('description')
            ->addTextArea('keywords')
            ->addTextArea(
                'controller',
                'Controller',
                'Controller and action name that will handle this page. Example: NameController->someAction',
                null,
                ['emptyAllowed' => true, 'escape' => false]
            )
            ->addMultiSelect(
                'roles',
                'Roles',
                'If no value is selected, will be allowed to all (also as all selected).',
                Role::find(),
                null,
                ['using' => ['id', 'name']]
            );

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-pages']);

        $this->_setValidation($content);
    }

    /**
     * Set form validation.
     *
     * @param FieldSet $content Fieldset object.
     */
    protected function _setValidation($content)
    {
        $content->getValidation()
            ->add(
                'controller',
                new Regex(
                    [
                        'pattern' => '/$|(.*)Controller->(.*)Action/',
                        'message' => 'Wrong controller name. Example: NameController->someAction'
                    ]
                )
            );
    }
}