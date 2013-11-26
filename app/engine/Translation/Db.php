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

namespace Engine\Translation;

use Engine\Db\AbstractModel;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Column as PhalconColumn;
use Phalcon\Translate\Adapter;
use Phalcon\Translate\AdapterInterface;
use Phalcon\Translate\Exception;

/**
 * Database translation.
 *
 * @category  PhalconEye
 * @package   Engine\Translation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Db implements AdapterInterface
{
    /**
     * Database connection.
     *
     * @var Pdo
     */
    protected $_db;

    /**
     * Locale model object.
     *
     * @var AbstractModel
     */
    protected $_locale;


    /**
     * Locale model class.
     *
     * @var AbstractModel
     */
    protected $_model;

    /**
     * Translation model object.
     *
     * @var string
     */
    protected $_translationModel;

    /**
     * Translation constructor.
     *
     * @param array $options Translation options.
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        $this->_db = $options['db'];
        /** @var AbstractModel $model */
        $this->_model = $model = $options['model'];
        $this->_translationModel = $options['translationModel'];

        $this->_locale = $model::find(array(
            'conditions' => 'locale = :locale:',
            'bind' => (array(
                    "locale" => $options['locale']
                )),
            'bindTypes' => (array(
                    "locale" => PhalconColumn::BIND_PARAM_STR
                ))
        ))->getFirst();

        if (!$this->_locale) {
            $this->_locale = $model::findFirst("locale = 'en'");
        }
    }

    /**
     * Returns the translation string of the given key.
     *
     * @param string $translateKey Key.
     * @param array  $placeholders Placeholders.
     *
     * @return string
     */
    public function _($translateKey, $placeholders = null)
    {
        return $this->query($translateKey, $placeholders);
    }


    /**
     * Returns the translation related to the given key.
     *
     * @param string $index        Index name (key).
     * @param array  $placeholders Placeholders.
     *
     * @return    string
     */
    public function query($index, $placeholders = null)
    {
        if (!$this->_locale || empty($index)) {
            return $index;
        }

        // cleanup
        $index = preg_replace('~[\r\n]+~', '', $index);

        $translation = $this->get($index);

        if (!$translation) {
            // remember this translation
            $translationModel = $this->_translationModel;
            $translation = new $translationModel();
            $translation->languageId = $this->_locale->id;
            $translation->original = $index;
            $translation->translated = $index;
            $translation->save();

            return $index;
        }

        $translated = $translation->translated;

        if ($placeholders == null) {
            return $translated;
        }

        if (is_array($placeholders)) {
            foreach ($placeholders as $key => $value) {
                $translated = str_replace('%' . $key . '%', $value, $translated);
            }

        }

        return $translated;
    }

    /**
     * Check whether is defined a translation key in the internal array.
     *
     * @param string $index Key name.
     *
     * @return bool
     */
    public function exists($index)
    {
        return $this->get($index) !== null;
    }

    /**
     * Get by key.
     *
     * @param string $index Key name.
     *
     * @return mixed
     */
    private function get($index)
    {
        $translationModel = $this->_translationModel;

        return $translationModel::find(array(
            'conditions' => 'original = :content: AND language_id = :id:',
            'bind' => (array(
                    "content" => $index,
                    "id" => $this->_locale->id
                )),
            'bindTypes' => (array(
                    "content" => PhalconColumn::BIND_PARAM_STR,
                    "id" => PhalconColumn::BIND_PARAM_INT
                ))
        ))->getFirst();
    }

}