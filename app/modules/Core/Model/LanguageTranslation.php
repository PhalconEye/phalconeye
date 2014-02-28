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

namespace Core\Model;

use Engine\Db\AbstractModel;
use Engine\Translation\TranslationModelInterface;

/**
 * Language translation.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("language_translations")
 * @BelongsTo("language_id", "\Core\Model\Language", "id", {
 *  "alias": "Language"
 * })
 */
class LanguageTranslation extends AbstractModel implements TranslationModelInterface
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="language_id", size="11")
     */
    public $language_id;

    /**
     * @Column(type="string", nullable=true, column="scope", size="25")
     */
    public $scope = null;

    /**
     * @Column(type="text", nullable=false, column="original")
     */
    public $original;

    /**
     * @Column(type="text", nullable=false, column="translated")
     */
    public $translated = null;

    /**
     * @Column(type="boolean", nullable=false, column="checked")
     */
    public $checked = false;

    /**
     * Set scope.
     *
     * @param string $scope Scope name.
     *
     * @return mixed
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Set language id.
     *
     * @param int $languageId Language id.
     *
     * @return mixed
     */
    public function setLanguageId($languageId)
    {
        $this->language_id = $languageId;
    }

    /**
     * Set translation original text.
     *
     * @param string $text Original text.
     *
     * @return mixed
     */
    public function setOriginal($text)
    {
        $this->original = $text;
    }

    /**
     * Set translated text.
     *
     * @param string $text Translated text.
     *
     * @return mixed
     */
    public function setTranslated($text)
    {
        $this->translated = $text;
    }

    /**
     * Get translated data.
     *
     * @return string
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * Return the related "Language" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return Language
     */
    public function getLanguage($arguments = [])
    {
        return $this->getRelated('Language', $arguments);
    }
}