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

use Core\Model\PageModel;

/**
 * Edit page.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Page
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PageEditForm extends PageCreateForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this
            ->setTitle('Edit Page')
            ->setDescription('Edit this page.');

        $this->getFieldSet(self::FIELDSET_FOOTER)
            ->clearElements()
            ->addButton('save')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-pages']);

        if ($this->_currentPageObject->type == PageModel::PAGE_TYPE_HOME) {
            $this->getFieldSet(self::FIELDSET_CONTENT)
                ->remove('url')
                ->remove('controller')
                ->remove('roles[]');
        }
    }
}