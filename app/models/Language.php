<?php


class Language extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $locale;

    /**
     * @var string
     * @form_type fileField
     */
    protected $icon = null;

    public function initialize()
    {
        $this->hasMany("id", "LanguageTranslation", "language_id");
    }

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Method to set the value of field locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Method to set the value of field icon
     *
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }


    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns the value of field icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    public function getSource()
    {
        return "languages";
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
        $languageFile = ROOT_PATH . '/app/var/languages/' . $this->locale . '.php';
        @unlink($languageFile);

        $this->getLanguageTranslation()->delete();
    }

    public function generatePHP()
    {
        $translations = $this->getLanguageTranslation();
        $messages = array();
        foreach($translations as $translation){
            $messages[$translation->getOriginal()] = $translation->getTranslated();
        }

        $file = ROOT_PATH . '/app/var/languages/' . $this->locale . '.php';
        file_put_contents($file, '<?php ' . PHP_EOL . PHP_EOL . '$messages = ' . var_export($messages, true).';');
    }

    private function quote($string)
    {
        return '"' . str_replace(array("\r", "\n", '"'), array('', '\n', '\\"'), $string) . '"';
    }
}
