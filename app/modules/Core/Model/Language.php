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
use Phalcon\Mvc\Model\Message;

/**
 * Language.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
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
    const
        /**
         * Icon files location.
         */
        LANGUAGE_ICON_LOCATION = 'files/languages/',

        /**
         * Compiled languages location.
         */
        LANGUAGE_CACHE_LOCATION = '/app/var/cache/languages/';

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
     * @Column(type="string", nullable=false, column="language", size="2")
     */
    public $language;

    /**
     * @Column(type="string", nullable=false, column="locale", size="5")
     */
    public $locale;

    /**
     * @Column(type="string", nullable=true, column="icon", size="255")
     */
    public $icon = null;

    /**
     * Get cache file location.
     *
     * @return string
     */
    public function getCacheLocation()
    {
        return ROOT_PATH . self::LANGUAGE_CACHE_LOCATION . $this->language . '.php';
    }

    /**
     * Return the related "LanguageTranslation" model.
     *
     * @param array $arguments Model arguments.
     *
     * @return LanguageTranslation[]
     */
    public function getLanguageTranslation($arguments = [])
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
        $condition = "language = '" . $this->language . "' AND locale = '" . $this->locale . "'";

        if (!empty($this->id)) {
            $condition .= "AND id != " . $this->id;
        }

        $isValid = !(bool)Language::findFirst($condition);
        if (!$isValid) {
            $this->appendMessage(
                new Message(sprintf('Language "%s" with locale "%s" already exists.', $this->language, $this->locale))
            );
        }

        return $isValid;
    }

    /**
     * Before entity removal.
     *
     * @return void
     */
    public function beforeDelete()
    {
        if (file_exists($this->getCacheLocation())) {
            @unlink($this->getCacheLocation());
        }

        if (!empty($this->icon) && file_exists(PUBLIC_PATH . '/' . $this->icon)) {
            @unlink(PUBLIC_PATH . '/' . $this->icon);
        }

        $this->getLanguageTranslation()->delete();
    }

    /**
     * Get full icon path.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->getDI()->getUrl()->get($this->icon);
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
        $messages = [];
        foreach ($translations as $translation) {
            $messages[$translation->original] = $translation->translated;
        }

        $file = $config->application->cache->cacheDir . '../languages/' . $this->language . '.php';
        file_put_contents($file, '<?php ' . PHP_EOL . PHP_EOL . '$messages = ' . var_export($messages, true) . ';');
    }
}
