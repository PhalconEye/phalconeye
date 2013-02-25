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
     * Method to set the value of field params
     *
     * @param string $params
     * @param string $encode
     */
    public function setParams($params, $encode = true)
    {
        if ($encode)
            $this->params = json_encode($params);
        else{
            $this->params = $params;
        }
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
     * Returns the value of field params
     *
     * @return string
     */
    public function getParams()
    {
        return (array)json_decode($this->params);
    }

    public function getSource()
    {
        return "content";
    }
}
