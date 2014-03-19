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

use Core\Model\Page;

/**
 * Edit page.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Page
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Edit extends Create
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
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-pages']);

        if ($this->_currentPageObject->type == Page::PAGE_TYPE_HOME) {
            $this->getFieldSet(self::FIELDSET_CONTENT)
                ->remove('url')
                ->remove('controller')
                ->remove('roles[]');
        }
    }
}