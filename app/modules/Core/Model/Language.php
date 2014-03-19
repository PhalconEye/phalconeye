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

use Engine\Behaviour\DIBehaviour;
use Engine\Db\AbstractModel;
use Engine\Exception;
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

    const
        /**
         * Batch size for importing language data.
         */
        LANGUAGE_IMPORT_BATCH_SIZE = 100;

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
     * Parse import data.
     * Import translations and language if needed.
     *
     * @param DIBehaviour $di   Dependency injection.
     * @param array       $data Data to parse.
     *
     * @throws Exception
     * @return Language
     */
    public static function parseImportData($di, $data)
    {
        if (empty($data['language']) || empty($data['locale']) || empty($data['locale'])) {
            throw new Exception(
                $di->getI18n()->_(
                    'Language translations package must contains fields (not empty): name, language, locale...'
                )
            );
        }

        /**
         * Get related language.
         */
        $language = Language::findFirst(["language = '{$data['language']}' AND locale = '{$data['locale']}'"]);
        if (!$language) {
            $language = new Language();
            $language->assign($data);
            if (!$language->save()) {
                throw new Exception(implode(', ', $language->getMessages()));
            }
        }

        /**
         * Import into database.
         */
        $table = LanguageTranslation::getTableName();
        $sql = "INSERT IGNORE INTO `{$table}` (language_id, scope, original, translated) VALUES ";
        $sqlValues = [];
        $counter = 0;
        $totals = [];
        foreach ($data['content'] as $scope => $translations) {
            $totals[$scope] = 0;
            foreach ($translations as $original => $translated) {
                $sqlValues[] = PHP_EOL .
                    sprintf(
                        '(%d, "%s", "%s", "%s")',
                        $language->getId(),
                        mysql_real_escape_string($scope),
                        mysql_real_escape_string($original),
                        mysql_real_escape_string($translated)
                    );

                $counter++;
                $totals[$scope]++;
                if ($counter == self::LANGUAGE_IMPORT_BATCH_SIZE) {
                    $counter = 0;
                    $sqlValues = '';
                    $di->getModelsManager()->execute($sql . implode(',', $sqlValues));
                }
            }
        }

        if (!empty($sqlValues)) {
            $di->getDb()->execute($sql . implode(',', $sqlValues));
        }

        return [$language, $totals];
    }

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

        $iconPath = PUBLIC_PATH . '/' . $this->icon;
        if (!empty($this->icon) && file_exists($iconPath) && is_file($iconPath)) {
            @unlink($iconPath);
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
        if (empty($this->icon)) {
            return '';
        }
        return $this->getDI()->getUrl()->get($this->icon);
    }

    /**
     * Generate php files from database data.
     *
     * @return void
     */
    public function toPhp()
    {
        $translations = $this->getLanguageTranslation();
        $messages = [];
        foreach ($translations as $translation) {
            $messages[$translation->original] = $translation->translated;
        }

        return '<?php ' . PHP_EOL . PHP_EOL . '$messages = ' . var_export($messages, true) . ';';
    }

    /**
     * Generate array with language translations.
     *
     * @param array $scope Translations scopes.
     *
     * @return string
     */
    public function toTranslationsArray(array $scope = [])
    {
        $whereCondition = 'language_id = :language_id:';

        if (!empty($scope)) {
            $whereCondition .= ' AND scope IN ("' . implode('","', $scope) . '")';
        }

        $result = LanguageTranslation::getBuilder()
            ->where(
                $whereCondition,
                ['language_id' => $this->getId()]
            )
            ->getQuery()
            ->execute();

        $data = [];
        foreach ($result as $row) {
            $data[$row->scope][$row->original] = $row->translated;
        }

        $result = [
            'info' => 'PhalconEye Language Package',
            'version' => PHALCONEYE_VERSION,
            'date' => date('d-M-Y H:i'),
            'name' => $this->name,
            'language' => $this->language,
            'locale' => $this->locale,
            'content' => $data
        ];

        return $result;
    }

    /**
     * Generate json content with language translations.
     *
     * @param array $scope Translations scopes.
     *
     * @return string
     */
    public function toJson(array $scope = [])
    {
        return json_encode($this->toTranslationsArray($scope), JSON_PRETTY_PRINT);
    }
}
