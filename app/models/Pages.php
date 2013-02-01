<?php

class Pages extends \Phalcon\Mvc\Model 
{

    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    protected $title;

    /**
     * @var string
     *
     */
    protected $url;

    /**
     * @var string
     * @form_type textArea
     *
     */
    protected $description;

    /**
     * @var string
     * @form_type textArea
     *
     */
    protected $keywords;

    /**
     * @var string
     * @form_type selectStatic
     *
     */
    protected $layout = 'middle';

    /**
     * @var string
     *
     */
    protected $controller = null;

    /**
     * @var int
     *
     */
    protected $view_count = 0;


    /**
     * Method to set the value of field id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Method to set the value of field title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Method to set the value of field url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Method to set the value of field keywords
     *
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Method to set the value of field layout
     *
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Method to set the value of field controller
     *
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Method to set the value of field view_count
     *
     * @param int $view_count
     */
    public function setViewCount($view_count)
    {
        $this->view_count = $view_count;
    }


    /**
     * Returns the value of field id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the value of field url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the value of field keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Returns the value of field layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Returns the value of field controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the value of field view_count
     *
     * @return int
     */
    public function getViewCount()
    {
        return $this->view_count;
    }

    public function getSource()
    {
        return "pages";
    }

    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
            "field" => "url",
            'min' => 1
        )));

        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'title'
        )));

        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            'field' => 'url'
        )));


        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
