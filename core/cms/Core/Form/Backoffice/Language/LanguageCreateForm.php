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

namespace Core\Form\Backoffice\Language;

use Core\Form\FileForm;
use Core\Model\LanguageModel;
use Engine\Db\AbstractModel;
use Engine\Form\FieldSet;
use Phalcon\Validation\Validator\StringLength;

/**
 * Create language form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Language
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class LanguageCreateForm extends FileForm
{
    /**
     * Create form.
     *
     * @param AbstractModel $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new LanguageModel();
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
            ->setTitle('Language Creation')
            ->setDescription('Create new language.');

        $content = $this->addContentFieldSet()
            ->addText('name')
            ->addText('language')
            ->addText('locale')
            ->addFile('icon', null, null, true);

        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-languages']);

        $this->_setValidation($content);
    }

    /**
     * Set form validation.
     *
     * @param FieldSet $content Content object.
     *
     * @return void
     */
    protected function _setValidation($content)
    {
        $content
            ->setRequired('language')
            ->setRequired('locale');

        $content->getValidation()
            ->add('language', new StringLength(['min' => 2, 'max' => 2]))
            ->add('locale', new StringLength(['min' => 5, 'max' => 5]));

        $this->setImageTransformation(
            'icon',
            [
                'adapter' => 'GD',
                'resize' => [32, 32]
            ]
        );
    }
}