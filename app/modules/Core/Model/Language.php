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

use Engine\Db\AbstractModel;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Language.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("languages")
 * @HasMany("id", "\Core\Model\LanguageTranslation", "language_id", {
 *  "alias": "LanguageTranslation"
 * })
 */
class Language extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name", size="50")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="locale", size="50")
     */
    public $locale;

    /**
     * @Column(type="string", nullable=true, column="icon", size="255")
     */
    public $icon = null;


    /**
     * Return the related "LanguageTranslation" model.
     *
     * @param array $arguments Model arguments.
     *
     * @return LanguageTranslation[]
     */
    public function getLanguageTranslation($arguments = array())
    {
        return $this->getRelated('LanguageTranslation', $arguments);
    }

    /**
     * Model validation.
     *
     * @return bool
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'locale'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Before entity removal.
     *
     * @return void
     */
    public function beforeDelete()
    {
        $config = $this->getDI()->get('config');
        $languageFile = $config->application->cache->cacheDir . '../languages/' . $this->locale . '.php';
        @unlink($languageFile);

        $this->getLanguageTranslation()->delete();
    }

    /**
     * Generate php files from database data.
     *
     * @return void
     */
    public function generatePHP()
    {
        $translations = $this->getLanguageTranslation();
        $config = $this->getDI()->get('config');
        $messages = array();
        foreach ($translations as $translation) {
            $messages[$translation->original] = $translation->translated;
        }

        $file = $config->application->cache->cacheDir . '../languages/' . $this->locale . '.php';
        file_put_contents($file, '<?php ' . PHP_EOL . PHP_EOL . '$messages = ' . var_export($messages, true) . ';');
    }
}
