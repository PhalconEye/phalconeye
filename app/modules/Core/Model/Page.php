<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

/**
 * Page.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("pages")
 * @HasMany("id", '\Core\Model\Content', "page_id", {
 *  "alias": "Content"
 * })
 * @Acl(actions={"show_views"}, options={"page_footer"})
 */
class Page extends \Engine\Db\AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="title", size="255")
     */
    public $title;

    /**
     * @Column(type="string", nullable=true, column="type", size="25")
     */
    public $type = null;

    /**
     * @Column(type="string", nullable=true, column="url", size="255")
     */
    public $url;

    /**
     * @Column(type="text", nullable=false, column="description")
     */
    public $description;

    /**
     * @Column(type="text", nullable=false, column="keywords")
     */
    public $keywords;

    /**
     * @Column(type="string", nullable=false, column="layout", size="50")
     */
    public $layout = 'middle';

    /**
     * @Column(type="string", nullable=true, column="controller", size="50")
     */
    public $controller = null;

    /**
     * @Column(type="string", nullable=true, column="roles", size="150")
     */
    protected $roles = null;

    /**
     * @Column(type="integer", nullable=false, column="view_count", size="11")
     */
    public $view_count = 0;

    /**
     * Returns the value of field roles
     *
     * @return string
     */
    public function getRoles()
    {
        if (is_array($this->roles))
            return $this->roles;

        return json_decode($this->roles);
    }

    /**
     * Prepare json string to object to interract
     */
    public function prepareRoles()
    {
        if (!is_array($this->roles))
            $this->roles = json_decode($this->roles);
    }

    /**
     * Set widgets data related to page
     *
     * @param array $widgets
     */
    public function setWidgets($widgets = array())
    {
        if (!$widgets)
            $widgets = array();

        $currentPageWidgets = $this->getDI()->get('session')->get('admin-pages-manage', array());

        // updating
        $existing_widgets = $this->getWidgets();
        $widgets_ids_to_remove = array(); // widgets that we need to remove
        // looping all exisitng widgets and looping new widgets
        // looking for new, changed, and deleted actions
        /** @var Content $ex_widget */
        foreach ($existing_widgets as $ex_widget) {
            $founded = false; // indicates if widgets founded in new array
            $orders = array();

            foreach ($widgets as $item) {
                if (empty($currentPageWidgets[$item['widget_index']]))
                    continue;
                $itemData = $currentPageWidgets[$item['widget_index']];

                if (empty($orders[$item["layout"]]))
                    $orders[$item["layout"]] = 1;
                else
                    $orders[$item["layout"]]++;

                if ($ex_widget->id == $itemData["id"]) {
                    $ex_widget->layout = $item["layout"];
                    $ex_widget->widget_order = $orders[$item["layout"]];
                    $ex_widget->setParams($itemData["params"]);
                    $ex_widget->save();
                    $founded = true;
                }
            }

            if (!$founded) {
                $widgets_ids_to_remove[] = $ex_widget->id;
            }
        }

        // inserting
        $orders = array();
        foreach ($widgets as $item) {
            if (empty($currentPageWidgets[$item['widget_index']])) {
                if ($item['widget_index'] == 'NaN') { // insert with empty parameters
                    $content = new Content();
                    $content->page_id = $this->id;
                    $content->widget_id = $item["widget_id"];
                    $content->layout = $item["layout"];
                    $content->setParams(array());
                    $content->widget_order = $orders[$item["layout"]];
                    $content->save();
                }
                continue;
            }
            $itemData = $currentPageWidgets[$item['widget_index']];

            if (empty($orders[$item["layout"]]))
                $orders[$item["layout"]] = 1;
            else
                $orders[$item["layout"]]++;

            if ($itemData["id"] == 0) { // need to be inserted
                $content = new Content();
                $content->page_id = $this->id;
                $content->widget_id = $item["widget_id"];
                $content->layout = $item["layout"];
                $content->setParams($itemData["params"]);
                $content->widget_order = $orders[$item["layout"]];
                $content->save();
            }
        }

        if (!empty($widgets_ids_to_remove)) {
            $rowsToRemove = Content::find("id IN (" . implode(',', $widgets_ids_to_remove) . ")");
            $rowsToRemove->delete();
        }


    }

    /**
     * Get related widgets data
     *
     * @return \Engine\Model\ResultsetInterface
     */
    public function getWidgets()
    {
        return Content::find(array(
            "page_id = '{$this->id}'",
            "order" => "widget_order",
        ));
    }

    public function incrementViews()
    {
        $this->view_count++;
        $this->save();
    }

    /**
     * Check if this page is allowed to view
     *
     * @return bool
     */
    public function isAllowed()
    {
        $viewer = \User\Model\User::getViewer();
        $roles = $this->getRoles();
        if (empty($roles)) return true;
        return in_array($viewer->getRoleId(), $roles);
    }

    public function validation()
    {
        if ($this->url !== null) {
            $this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
                "field" => "url",
                'min' => 1
            )));
        }

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

    protected function beforeDelete()
    {
        $this->getWidgets()->delete();
    }

    protected function beforeSave()
    {
        if (is_array($this->roles) && !empty($this->roles)) {
            $this->roles = json_encode($this->roles);
        } else {
            $this->roles = null;
        }
    }

}
