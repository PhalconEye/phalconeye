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

namespace Core\Grid\Backoffice;

use Core\Grid\CoreGrid;
use Core\Model\LanguageModel;
use Engine\Config;
use Engine\Grid\GridItem;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\ViewInterface;

/**
 * Language translation grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class LanguageTranslationGrid extends CoreGrid
{
    /**
     * Current language.
     *
     * @var LanguageModel
     */
    protected $_language;

    /**
     * Create grid.
     *
     * @param ViewInterface $view     View object.
     * @param LanguageModel $language language object.
     */
    public function __construct(ViewInterface $view, LanguageModel $language)
    {
        $this->_language = $language;
        parent::__construct($view);
    }

    /**
     * Get main select builder.
     *
     * @return Builder
     */
    public function getSource()
    {
        $builder = new Builder();
        $builder
            ->from('Core\Model\LanguageTranslationModel')
            ->where('language_id = ' . $this->_language->getId());

        $showUntranslated = (bool)$this->getDI()->getRequest()->get('untranslated', 'int', 0);

        if ($showUntranslated) {
            $builder->where("original = translated");
        }

        if ($search = $this->getDI()->getRequest()->get('search')) {
            $builder
                ->where("original LIKE '%{$search}%'")
                ->orWhere("translated LIKE '%{$search}%'");
        }

        return $builder;
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        return [
            'Edit' => ['attr' => ['onclick' => 'editItem(' . $item['id'] . ');return false;']],
            'Delete' => [
                'href' => ['for' => 'backoffice-languages-delete-item', 'id' => $item['id'], 'lang' => $item['language_id']],
                'attr' => ['class' => 'grid-action-delete']
            ]
        ];
    }

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    protected function _initColumns()
    {
        $language = $this->_language;

        $this
            ->addTextColumn('scope', 'Scope')
            ->addTextColumn('original', 'Original')
            ->addTextColumn(
                'translated',
                'Translated',
                [
                    self::COLUMN_PARAM_OUTPUT_LOGIC =>
                        function ($item) use ($language) {
                            if (
                                $language->language != Config::CONFIG_DEFAULT_LANGUAGE &&
                                !$item['checked']
                            ) {
                                return '<i class="untranslated">' . $item['translated'] . '</i>';
                            }
                            return $item['translated'];
                        }
                ]
            );
    }
}