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

/**
 * Content.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("content")
 * @BelongsTo("widget_id", "\Core\Model\Widget", "id", {
 *  "alias": "Widget"
 * })
 * @BelongsTo("page_id", "\Core\Model\Page", "id", {
 *  "alias": "Page"
 * })
 */
class Content extends AbstractModel
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
    public $params;

    /**
     * Return the related "Widget" model.
     *
     * @param array $arguments Model arguments.
     *
     * @return Widget
     */
    public function getWidget($arguments = [])
    {
        return $this->getRelated('Widget', $arguments);
    }

    /**
     * Method to set the value of field params.
     *
     * @param array $params Params data.
     * @param bool  $encode Encode into json.
     *
     * @return void
     */
    public function setParams($params, $encode = true)
    {
        if ($encode) {
            $this->params = json_encode($params);
        } else {
            $this->params = $params;
        }
    }

    /**
     * Returns the value of params field.
     *
     * @return array
     */
    public function getParams()
    {
        $params = (array)json_decode($this->params);
        $params['content_id'] = $this->id;

        return $params;
    }
}
