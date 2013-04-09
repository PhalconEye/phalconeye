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

class Form_Element_RemoteFile extends Form_Element implements Form_ElementInterface
{

    private $_editorUrl = '/external/ajaxplorer/?external_selector_type=popup&relative_path=/public/files';
    protected $_description;
    protected $_value;

    public function __construct($name, $attributes=null){
        if (!empty($attributes['value'])){
            $this->_value = $attributes['value'];
        }

        parent::__construct($name, $attributes);
    }

    /**
     * If element is need to be rendered in default layout
     *
     * @return bool
     */
    public function useDefaultLayout()
    {
        return true;
    }

    /**
     * Sets the element description
     *
     * @param string $description
     * @return Form_ElementInterface
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }


    /**
     * Returns the element's description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    public function setDefault($value){
        $this->_value = $value;
        parent::setDefault($value);
    }


    public function render()
    {
        if ($this->_value === null)
            $this->_value = $this->getForm()->getValue($this->getName());

        $attributes = $this->getAttributes();
        $buttonTitle = 'Select file';
        if (isset($attributes['title'])) {
            $buttonTitle = $attributes['title'];
            unset($attributes['title']);
        }
        return sprintf('
            <div class="form_element_remote_file">
                <input type="text" name="%s" id="%s" value="%s" />
                <input onclick="PE.ajaxplorer.openAjaxplorerPopup($(this).parent(), \'%s\', \'%s\');" type="button" class="btn btn-primary" value="%s"/>
            </div>',
            $this->getName(), $this->getName(), $this->_value, $this->_editorUrl, $buttonTitle, $buttonTitle);
    }

}