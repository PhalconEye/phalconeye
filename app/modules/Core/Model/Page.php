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
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use User\Model\User;

/**
 * Page.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("pages")
 * @HasMany("id", '\Core\Model\Content', "page_id", {
 *  "alias": "Content"
 * })
 * @Acl(actions={"show_views"}, options={"page_footer"})
 */
class Page extends AbstractModel
{
    const
        /**
         * Layout types.
         */
        LAYOUT_RIGHT_MIDDLE_LEFT = 'right_middle_left',
        LAYOUT_MIDDLE_LEFT = 'middle_left',
        LAYOUT_RIGHT_MIDDLE = 'right_middle',
        LAYOUT_MIDDLE = 'middle',
        LAYOUT_TOP_RIGHT_MIDDLE_LEFT = 'top_right_middle_left',
        LAYOUT_TOP_MIDDLE_LEFT = 'top_middle_left',
        LAYOUT_TOP_RIGHT_MIDDLE = 'top_right_middle',
        LAYOUT_TOP_MIDDLE = 'top_middle',
        LAYOUT_RIGHT_MIDDLE_LEFT_BOTTOM = 'right_middle_left_bottom',
        LAYOUT_MIDDLE_LEFT_BOTTOM = 'middle_left_bottom',
        LAYOUT_RIGHT_MIDDLE_BOTTOM = 'right_middle_bottom',
        LAYOUT_MIDDLE_BOTTOM = 'middle_bottom';

    const
        /**
         * Layout icon path
         */
        LAYOUT_ICON_PATH = 'assets/img/core/admin/content/',

        /**
         * Icon extension name.
         */
        LAYOUT_ICON_EXTENSION = '.png';

    const
        /**
         * ACL action name for views.
         */
        ACTION_SHOW_VIEWS = 'show_views',

        /**
         * Acl option for page footer.
         */
        OPTION_PAGE_FOOTER = 'page_footer';

    const
        /**
         * Home page type.
         */
        PAGE_TYPE_HOME = 'home';

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
     * @Column(type="text", nullable=true, column="description")
     */
    public $description = null;

    /**
     * @Column(type="text", nullable=true, column="keywords")
     */
    public $keywords = null;

    /**
     * @Column(type="string", nullable=false, column="layout", size="50")
     */
    public $layout = self::LAYOUT_MIDDLE;

    /**
     * @Column(type="string", nullable=true, column="controller", size="50")
     */
    public $controller = null;

    /**
     * @Column(type="string", nullable=true, column="roles", size="150")
     */
    public $roles = null;

    /**
     * @Column(type="integer", nullable=false, column="view_count", size="11")
     */
    public $view_count = 0;

    /**
     * Set widgets data related to page.
     *
     * @param array $widgets Widgets data.
     *
     * @return void
     */
    public function setWidgets($widgets = [])
    {
        if (!$widgets) {
            $widgets = [];
        }

        $currentPageWidgets = $this->getDI()->get('session')->get('admin-pages-manage', []);

        // Updating.
        $existing_widgets = $this->getWidgets();
        $widgets_ids_to_remove = []; // widgets that we need to remove
        // looping all existing widgets and looping new widgets
        // looking for new, changed, and deleted actions
        /** @var Content $ex_widget */
        foreach ($existing_widgets as $ex_widget) {
            $founded = false; // indicates if widgets founded in new array
            $orders = [];
            foreach ($widgets as $item) {
                if (empty($currentPageWidgets[$item['widget_index']])) {
                    continue;
                }
                $itemData = $currentPageWidgets[$item['widget_index']];

                if (empty($orders[$item["layout"]])) {
                    $orders[$item["layout"]] = 1;
                } else {
                    $orders[$item["layout"]]++;
                }

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

        // Inserting.
        $orders = [];
        foreach ($widgets as $item) {
            if (empty($orders[$item["layout"]])) {
                $orders[$item["layout"]] = 0; // Inserting must have bigger priority.
            } else {
                $orders[$item["layout"]]++;
            }

            if (empty($currentPageWidgets[$item['widget_index']])) {
                if ($item['widget_index'] == 'NaN') {
                    // Insert with empty parameters.
                    $content = new Content();
                    $content->page_id = $this->id;
                    $content->widget_id = $item["widget_id"];
                    $content->layout = $item["layout"];
                    $content->setParams([]);
                    $content->widget_order = $orders[$item["layout"]];
                    $content->save();
                }
                continue;
            }
            $itemData = $currentPageWidgets[$item['widget_index']];

            if ($itemData["id"] == 0) {
                // Need to be inserted.
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
     * Get related widgets data.
     *
     * @return ResultsetInterface
     */
    public function getWidgets()
    {
        return Content::find(
            [
                "page_id = '{$this->id}'",
                "order" => "widget_order",
            ]
        );
    }

    /**
     * Increment views.
     *
     * @return void
     */
    public function incrementViews()
    {
        $this->view_count++;
        $this->save();
    }

    /**
     * Check if this page is allowed to view.
     *
     * @return bool
     */
    public function isAllowed()
    {
        $viewer = User::getViewer();
        if (empty($this->roles)) {
            return true;
        }

        return in_array($viewer->role_id, $this->roles);
    }

    /**
     * Validation logic.
     *
     * @return bool
     */
    public function validation()
    {
        if ($this->url !== null) {
            $this->validate(new StringLength(["field" => "url", 'min' => 1]));
        }

        $this->validate(new PresenceOf(['field' => 'title']));
        $this->validate(new Uniqueness(['field' => 'url']));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Get page icon path.
     *
     * @return string
     */
    public function getLayoutIcon()
    {
        return self::LAYOUT_ICON_PATH . $this->layout . self::LAYOUT_ICON_EXTENSION;
    }

    /**
     * Logic before removal.
     *
     * @return void
     */
    protected function beforeDelete()
    {
        $this->getWidgets()->delete();
    }

    /**
     * Spell some logic after fetching.
     *
     * @return void
     */
    protected function afterFetch()
    {
        if (!empty($this->roles)) {
            $this->roles = json_decode($this->roles);
        }
    }

    /**
     * Logic before save.
     *
     * @return void
     */
    protected function beforeSave()
    {
        if (empty($this->roles)) {
            $this->roles = null;
        } elseif (is_array($this->roles)) {
            $this->roles = json_encode($this->roles);
        }
    }
}