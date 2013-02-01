<?php


class Content extends \Phalcon\Mvc\Model 
{

    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var int
     *
     */
    protected $page_id;

    /**
     * @var int
     *
     */
    protected $widget_id;

    /**
     * @var int
     *
     */
    protected $widget_order;

    /**
     * @var string
     *
     */
    protected $layout;

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $params;


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
     * Method to set the value of field page_id
     *
     * @param int $page_id
     */
    public function setPageId($page_id)
    {
        $this->page_id = $page_id;
    }

    /**
     * Method to set the value of field widget_id
     *
     * @param int $widget_id
     */
    public function setWidgetId($widget_id)
    {
        $this->widget_id = $widget_id;
    }

    /**
     * Method to set the value of field widget_order
     *
     * @param int $widget_order
     */
    public function setWidgetOrder($widget_order)
    {
        $this->widget_order = $widget_order;
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
     * Method to set the value of field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Method to set the value of field params
     *
     * @param string $params
     */
    public function setParams($params)
    {
        $this->params = $params;
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
     * Returns the value of field page_id
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Returns the value of field widget_id
     *
     * @return int
     */
    public function getWidgetId()
    {
        return $this->widget_id;
    }

    /**
     * Returns the value of field widget_order
     *
     * @return int
     */
    public function getWidgetOrder()
    {
        return $this->widget_order;
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
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getSource()
    {
        return "content";
    }
}
