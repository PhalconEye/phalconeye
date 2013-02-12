<?php


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
     * @var string
     * @form_type selectStatic
     *
     */
    protected $target;

    /**
     * @var integer
     *
     */
    protected $item_order = 0;

    /**
     * @var string
     *
     */
    protected $languages;


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
        return $this->languages;
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

    public function getHref(){
        if ($this->page_id){
            return "page/{$this->page_id}";
        }

        if ($this->url){
            return $this->url;
        }

        return 'javascript:;';
    }
}
