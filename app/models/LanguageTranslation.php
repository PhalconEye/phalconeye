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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class LanguageTranslation extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var integer
     *
     */
    protected $language_id;

    /**
     * @form_type textArea
     */
    protected $original;


    /**
     * @form_type textArea
     */
    protected $translated = NULL;



    public function initialize()
    {
        $this->belongsTo("language_id", "Language", "id");
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
     * Method to set the value of field language_id
     *
     * @param integer $language_id
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;
    }

    /**
     * Method to set the value of field original
     *
     * @param string $original
     */
    public function setOriginal($original)
    {
        $this->original = $original;
    }

    /**
     * Method to set the value of field translated
     *
     * @param string $translated
     */
    public function setTranslated($translated)
    {
        $this->translated = $translated;
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
     * Returns the value of field language_id
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * Returns the value of field original
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Returns the value of field translated
     *
     * @return string
     */
    public function getTranslated()
    {
        return $this->translated;
    }


    public function getSource()
    {
        return "language_translations";
    }

}
