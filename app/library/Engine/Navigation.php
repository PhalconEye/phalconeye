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

class Navigation
{

    /** @var array Items in navigation */
    protected $_items = array();

    /** @var string Html/Text before item title */
    protected $_itemPrependContent = '';

    /** @var string Html/Text after item title */
    protected $_itemAppendContent = '';

    /** @var string Class of navigation */
    protected $_listClass = 'nav';

    /** @var string Class of dropdown item */
    protected $_dropDownItemClass = "dropdown";

    /** @var string Class of dropdown item title */
    protected $_dropDownItemMenuClass = "dropdown-menu";

    /** @var string Class of dropdown item title */
    protected $_dropDownSubItemMenuClass = "dropdown-submenu";

    /** @var string Class of dropdown item switcher */
    protected $_dropDownItemToggleClass = "dropdown-toggle";

    /** @var string Class of dropdown header item */
    protected $_dropDownItemHeaderClass = "nav-header";

    /** @var string Class of dropdown divider item */
    protected $_dropDownItemDividerClass = "divider";

    /** @var string HTML code for dropdown icon */
    protected $_dropDownIcon = '<b class="caret"></b>';

    /** @var bool Set true to highlight dropdown top item */
    protected $_highlightActiveDropDownItem = true;

    /** @var string Currently active item, it can be name or href */
    protected $_activeItem = '';

    /** @var string Tag of the navigation */
    protected $_listTag = 'ul';

    /** @var string Tag of navigation item */
    protected $_listItemTag = 'li';


    public function __construct(){
        $this->_activeItem = substr(Phalcon\DI::getDefault()->get('request')->get('_url'), 1);
    }

    /** Set list class
     *
     * @param string $class
     *
     * @return Navigation
     * */
    public function setListClass($class)
    {
        $this->_listClass = $class;

        return $this;
    }

    /** Set dropdown item class
     *
     * @param string $class
     *
     * @return Navigation
     * */
    public function setDropDownItemClass($class){
        $this->_dropDownItemClass = $class;

        return $this;
    }

    /** Set dropdown menu class
     *
     * @param string $class
     *
     * @return Navigation
     * */
    public function setDropDownItemMenuClass($class){
        $this->_dropDownItemMenuClass = $class;

        return $this;
    }

    /** Set dropdown icon html
     *
     * @param string $html
     *
     * @return Navigation
     * */
    public function setDropDownIcon($html){
        $this->_dropDownIcon = $html;

        return $this;
    }

    /** Set true to highlight dropdown top item
     *
     * @param bool $flag
     *
     * @return Navigation
     * */
    public function setEnabledDropDownHighlight($flag = true){
        $this->_highlightActiveDropDownItem = $flag;

        return $this;
    }

    /** Set before content
     *
     * @param string $content
     *
     * @return Navigation
     */
    public function setItemPrependContent($content)
    {
        $this->_itemPrependContent = $content;

        return $this;
    }

    /** Set after content
     *
     * @param string $content
     *
     * @return Navigation
     */
    public function setItemAppendContent($content)
    {
        $this->_itemAppendContent = $content;

        return $this;
    }

    /** Set navigation list
     *
     * @param array $items
     *
     * @return Navigation
     */
    public function setItems($items = array())
    {
        if (empty($items))
            return $this;

        $this->_items = $items;
        return $this;
    }

    /** Set active item. It can be name or href.
     *
     * @param string $item
     *
     * @return Navigation
     */
    public function setActiveItem($item = '')
    {
        $this->_activeItem = $item;

        return $this;
    }

    /** Render navigation html
     *
     * @return string
     */
    public function render()
    {
        $content = '';
        if (empty($this->_items))
            return $content;

        // short names
        $lt = $this->_listTag;
        $lit = $this->_listItemTag;
        $lc = $this->_listClass;

        $content = "<{$lt} class='{$lc}'>";
        $content .= $this->_renderItems($this->_items);
        $content .= "</{$lt}>";
        return $content;
    }

    private function _renderItems($items, $isSubMenu = false)
    {

        $content = '';

        // short names
        $lt = $this->_listTag;
        $lit = $this->_listItemTag;
        $lc = $this->_listClass;
        $pc = $this->_itemPrependContent;
        $ac = $this->_itemAppendContent;
        $ddic = ($isSubMenu ? $this->_dropDownSubItemMenuClass : $this->_dropDownItemClass);
        $ddmc = ($isSubMenu ? '' : $this->_dropDownIcon);
        $ddimc = $this->_dropDownItemMenuClass;
        $dditc = $this->_dropDownItemToggleClass;
        $ddihc = $this->_dropDownItemHeaderClass;
        $ddidc = $this->_dropDownItemDividerClass;

        foreach ($items as $name => $item) {
            if (isset($item['items']) && !empty($item['items'])) { // dropdown menu item
                $active = ($name == $this->_activeItem || ($this->_highlightActiveDropDownItem && array_key_exists($this->_activeItem, $item['items'])) ? ' active' : '');
                $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                $linkTooltip = (!empty($item['tooltip']) ? 'title="' . $item['tooltip'] . '" data-placement="' . $item['tooltip_position'] . '"' : '');

                $content .= "<{$lit} class='{$ddic}{$active}'>";
                $content .= sprintf('<a %s %s href="javascript:;" class="%s" data-toggle="dropdown">%s%s%s%s</a>', $linkOnclick, $linkTooltip, $dditc, $pc, Phalcon\DI::getDefault()->get('trans')->query($item['title']), $ac, $ddmc);
                $content .= "<{$lt} class='{$ddimc}'>";
                foreach ($item['items'] as $key => $subitem) {
                    if (is_numeric($key) && !is_array($subitem)) {
                        if ($subitem == 'divider') {
                            $content .= "<{$lit} class='{$ddidc}'></{$lit}>";
                        } else {
                            $content .= "<{$lit} class='{$ddihc}'>";
                            $content .= Phalcon\DI::getDefault()->get('trans')->query($subitem);
                            $content .= "</{$lit}>";
                        }
                    } elseif (is_array($subitem)) {
                        $content .= $this->_renderItems(array(1 => $subitem), true);
                    } else {
                        $active = ($name == $this->_activeItem || $key == $this->_activeItem ? ' class="active"' : '');
                        $content .= "<{$lit}{$active}>";
                        if (strpos($key, 'http') === false && strpos($key, 'javascript:') === false && $key != '/')
                            $link = Phalcon\DI::getDefault()->get('url')->get($key);
                        $linkTarget = (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '');
                        $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                        $linkTooltip = (!empty($item['tooltip']) ? 'title="' . $item['tooltip'] . '" data-placement="' . $item['tooltip_position'] . '"' : '');

                        $content .= sprintf('<a %s %s %s href="%s">%s%s%s</a>', $linkTooltip, $linkTarget, $linkOnclick, $link, $pc, Phalcon\DI::getDefault()->get('trans')->query($subitem), $ac);
                        $content .= "</{$lit}>";
                    }

                }
                $content .= "</{$lt}>";
                $content .= "</{$lit}>";
            } else { // normal item
                $active = ($name == $this->_activeItem || $item['href'] == $this->_activeItem ? ' class="active"' : '');
                $linkTarget = (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '');
                $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                $linkTooltip = (!empty($item['tooltip']) ? 'title="' . $item['tooltip'] . '" data-placement="' . $item['tooltip_position'] . '"' : '');

                if (strpos($item['href'], 'http') === false && strpos($item['href'], 'javascript:') === false && $item['href'] != '/')
                    $item['href'] = Phalcon\DI::getDefault()->get('url')->get($item['href']);

                $content .= "<{$lit}{$active}>";
                $content .= sprintf('<a %s %s %s href="%s">%s%s%s</a>', $linkTooltip, $linkTarget, $linkOnclick, $item['href'], $pc, Phalcon\DI::getDefault()->get('trans')->query($item['title']), $ac);
                $content .= "</{$lit}>";
            }
        }

        return $content;
    }
}