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
 * @Source("content")
 * @BelongsTo("widget_id", "\Core\Model\Widget", "id", {
 *  "alias": "Widget"
 * })
 */
class Content extends \Engine\Model
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="page_id", size="11")
     */
    public $page_id;

    /**
     * @Column(type="integer", nullable=false, column="widget_id", size="11")
     */
    public $widget_id;

    /**
     * @Column(type="integer", nullable=false, column="widget_order", size="5")
     */
    public $widget_order = 0;

    /**
     * @Column(type="string", nullable=false, column="layout", size="50")
     */
    public $layout;

    /**
     * @Column(type="text", nullable=false, column="params")
     */
    protected $params;


    /**
     * Return the related "Widget"
     *
     * @return \Core\Model\Widget
     */
    public function getWidget($arguments = array())
    {
        return $this->getRelated('Widget', $arguments);
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
        else {
            $this->params = $params;
        }
    }

    /**
     * Returns the value of field params
     *
     * @return string
     */
    public function getParams()
    {
        $params = (array)json_decode($this->params);
        $params['content_id'] = $this->id;
        return $params;
    }

}
