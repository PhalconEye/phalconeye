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
namespace Core\Model;

/**
 * Language translation.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("language_translations")
 * @BelongsTo("language_id", "\Core\Model\Language", "id", {
 *  "alias": "Language"
 * })
 */
class LanguageTranslation extends \Engine\Db\AbstractModel
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
     * @Column(type="text", nullable=false, column="original")
     */
    public $original;

    /**
     * @Column(type="text", nullable=false, column="translated")
     */
    public $translated = NULL;

    /**
     * Return the related "Language"
     *
     * @return \Core\Model\Language
     */
    public function getLanguage($arguments = array())
    {
        return $this->getRelated('Language', $arguments);
    }

}
