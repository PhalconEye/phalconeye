<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Core\Model;

/**
 * @Source("languages")
 * @HasMany("id", "\Core\Model\LanguageTranslation", "language_id", {
 *  "alias": "LanguageTranslation"
 * })
 */
class Language extends \Engine\Db\Model
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
     * Return the related "LanguageTranslation"
     *
     * @return \Core\Model\LanguageTranslation[]
     */
    public function getLanguageTranslation($arguments = array())
    {
        return $this->getRelated('LanguageTranslation', $arguments);
    }

    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            'field' => 'locale'
        )));


        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    public function beforeDelete()
    {
        $languageFile = ROOT_PATH . '/app/var/cache/languages/' . $this->locale . '.php';
        @unlink($languageFile);

        $this->getLanguageTranslation()->delete();
    }

    public function generatePHP()
    {
        $translations = $this->getLanguageTranslation();
        $messages = array();
        foreach ($translations as $translation) {
            $messages[$translation->original] = $translation->translated;
        }

        $file = ROOT_PATH . '/app/var/cache/languages/' . $this->locale . '.php';
        file_put_contents($file, '<?php ' . PHP_EOL . PHP_EOL . '$messages = ' . var_export($messages, true) . ';');
    }
}
