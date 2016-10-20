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

use Core\Form\CoreForm;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Form for export translations.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Language
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class LanguageExportForm extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $builder = new Builder();
        $builder
            ->columns(['scope'])
            ->from('Core\Model\LanguageTranslation')
            ->distinct(true);

        $result = $builder->getQuery()->execute();
        $data = [];
        foreach ($result as $row) {
            $data[$row->scope] = $row->scope;
        }

        $this->addMultiSelect('scope', 'Scope', 'Select scopes of translations to export.', $data, $data);
    }
}