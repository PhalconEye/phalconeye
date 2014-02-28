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

namespace Core\Form\Admin\Language;

use Core\Form\CoreForm;

/**
 * Translations wizard form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Language
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Wizard extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->addHidden('translation_id')
            ->addTextArea(
                'original',
                'Original',
                null,
                null,
                [],
                ['class' => 'form-control textarea-readonly', 'readonly' => 'readonly']
            )
            ->addTextArea('translated', 'Translated')
            ->addTextArea(
                'suggestion',
                'Suggestion',
                'Translation suggestion',
                null,
                [],
                [
                    'class' => 'form-control textarea-readonly',
                    'readonly' => 'readonly',
                    'style' => "background: url('/assets/img/core/loader/black.gif')
                                #E0E0E0 no-repeat center center !important;"
                ]
            );
    }
}