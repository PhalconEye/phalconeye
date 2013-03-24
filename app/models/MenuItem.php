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

class MenuItem extends \Phalcon\Mvc\Model
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
    protected $title;

    /**
     * @var integer
     *
     */
    protected $menu_id;

    /**
     * @var integer
     *
     */
    protected $parent_id = null;

    /**
     * @var integer
     *
     */
    protected $page_id = null;

    /**
     * @var string
     *
     */
    protected $url;

    /**
     * Onclick js action
     */
    protected $onclick;

    /**
     * @var string
     * @form_type selectStatic
     *
     */
    protected $target;

    /**
     * Tooltip html
     * has no var type
     */
    protected $tooltip;

    /**
     * @var string
     * @form_type selectStatic
     *
     */
    protected $tooltip_position = 'top';

    /**
     * @var integer
     *
     */
    protected $item_order = 0;

    /**
     * @var string
     * @form_type select
     */
    protected $languages;

    /**
     * @var string
     * @form_type select
     */
    protected $roles = null;

    public function initialize()
    {
        $this->belongsTo("menu_id", "Menu", "id");
        $this->hasMany("id", "MenuItem", "parent_id");
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
     * Method to set the value of field title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Method to set the value of field menu_id
     *
     * @param integer $menu_id
     */
    public function setMenuId($menu_id)
    {
        $this->menu_id = $menu_id;
    }

    /**
     * Method to set the value of field parent_id
     *
     * @param integer $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * Method to set the value of field page_id
     *
     * @param integer $page_id
     */
    public function setPageId($page_id)
    {
        $this->page_id = $page_id;
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
     * Method to set the value of field position
     *
     * @param string $position
     */
    public function setTooltipPosition($position)
    {
        $this->tooltip_position = $position;
    }

    /**
     * Method to set the value of field item_order
     *
     * @param integer $item_order
     */
    public function setItemOrder($item_order)
    {
        $this->item_order = $item_order;
    }

    /**
     * Method to set the value of field languages
     *
     * @param string $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * Method to set the value of field roles
     *
     * @param string $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
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
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the value of field menu_id
     *
     * @return integer
     */
    public function getMenuId()
    {
        return $this->menu_id;
    }

    /**
     * Returns the value of field parent_id
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Returns parent object, it can be MenuItem or Menu (if there is no parent_id)
     *
     * @return MenuItem|Menu
     */
    public function getParent()
    {
        if ($this->parent_id) {
            return self::findFirst($this->parent_id);
        } else {
            return Menu::findFirst($this->menu_id);
        }
    }

    /**
     * Returns the value of field page_id
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->page_id;
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
     * Returns the value of field onclick
     *
     * @return string
     */
    public function getOnclick()
    {
        return str_replace('"', "'", $this->onclick); // double quetes escaping for tag onlick
    }

    /**
     * Returns the value of field tooltip
     *
     * @return string
     */
    public function getTooltip()
    {
        return str_replace('"', "'", $this->tooltip); // we need html to work well in attribute "title"
    }

    public function getTooltipPosition()
    {
        return $this->tooltip_position; // we need html to work well in attribute "title"
    }

    /**
     * Returns the value of field target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns the value of field item_order
     *
     * @return integer
     */
    public function getItemOrder()
    {
        return $this->item_order;
    }

    /**
     * Returns the value of field languages
     *
     * @return string
     */
    public function getLanguages()
    {
        if (is_array($this->languages))
            return $this->languages;

        return json_decode($this->languages);
    }

    /**
     * Prepare json string to object to interract
     */
    public function prepareLanguages(){
        if (!is_array($this->languages))
            $this->languages = json_decode($this->languages);
    }

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
    public function prepareRoles(){
        if (!is_array($this->roles))
            $this->roles = json_decode($this->roles);
    }

    public function getSource()
    {
        return "menu_items";
    }

    public static function getSourceStatic()
    {
        return "menu_items";
    }

    public function beforeDelete()
    {
        $flag = true;
        foreach ($this->getMenuItem() as $item) {
            $flag = $item->delete();
            if (!$flag) break;
        }
        return $flag;
    }


    public function beforeSave(){
        if (is_array($this->roles)){
            $this->roles = json_encode($this->roles);
        }

        if (is_array($this->languages)){
            $this->languages = json_encode($this->languages);
        }
    }

    public function getHref(){
        if ($this->page_id){
            return "page/{$this->page_id}";
        }

        if ($this->url){
            return $this->url;
        }

        return 'javascript:;';
    }

    public function isAllowed(){
        $valid = true;
        $viewer = User::getViewer();
        $roles = $this->getRoles();

        if (!empty($roles))
            $valid = in_array($viewer->getRoleId(), $roles);

        if (!$valid)
            return false;

        $valid = true;
        $locale = $this->getDI()->get('session')->get('locale', 'en');
        $languages = $this->getLanguages();

        if (!empty($languages))
            $valid = in_array($locale, $languages);

        return $valid;
    }
}
