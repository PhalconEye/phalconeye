<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Core\Form\Admin\Language;

use Core\Model\LanguageTranslation;
use Engine\Db\AbstractModel;
use Engine\Form;

/**
 * Create language item form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Language
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CreateItem extends Form
{
    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     */
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new LanguageTranslation();
        }

        parent::__construct($model);
    }

    /**
     * Initialize form.
     *
     * @return void
     */
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