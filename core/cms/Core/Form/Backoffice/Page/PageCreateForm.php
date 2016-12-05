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

namespace Core\Form\Backoffice\Page;

use Core\Form\CoreForm;
use Core\Model\PageModel;
use Engine\Db\AbstractModel;
use Engine\Form\Validator\RegexValidator;
use User\Model\RoleModel;

/**
 * Create page.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Page
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PageCreateForm extends CoreForm
{
    /**
     * @var PageModel
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
            $entity = new PageModel();
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
            ->addCheckbox('use_dynamic_layout', 'Use dynamic layout', 'Columns will be calculated during rendering 
            process. Otherwise columns are defined and if widgets are missing from some column - it still will be 
            visible.', 1, false, 0)
            ->addNumber('cache_lifetime', 'Cache', 'Life time (in seconds) of this page in cache. If missing - no 
        cache.')
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
                RoleModel::find(),
                null,
                ['using' => ['id', 'name']]
            );

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-pages']);

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
                new RegexValidator(
                    [
                        'pattern' => '/$|(.*)Controller->(.*)Action/',
                        'message' => 'Wrong controller name. Example: NameController->someAction'
                    ]
                )
            );
    }
}