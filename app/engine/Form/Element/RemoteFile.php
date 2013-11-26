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

namespace Engine\Form\Element;

use Engine\Form\Element;
use Engine\Form\Element\Traits\Description;
use Engine\Form\ElementInterface;

/**
 * Form element - Remote File (Ajaxplorer seleciton mode).
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RemoteFile extends Element implements ElementInterface
{
    use Description;

    /**
     * Ajaxplorer link.
     *
     * @var string
     */
    private $_editorUrl = '/external/ajaxplorer/?external_selector_type=popup&relative_path=/files';

    /**
     * Element value.
     *
     * @var
     */
    protected $_value;

    /**
     * Create Remote element.
     *
     * @param string $name       Element name.
     * @param null   $attributes Element attributes.
     */
    public function __construct($name, $attributes = null)
    {
        if (!empty($attributes['value'])) {
            $this->_value = $attributes['value'];
        }

        parent::__construct($name, $attributes);
    }

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function useDefaultLayout()
    {
        return true;
    }

    /**
     * Set default value.
     *
     * @param mixed $value Element value.
     *
     * @return ElementInterface
     */
    public function setDefault($value)
    {
        $this->_value = $value;
        parent::setDefault($value);

        return $this;
    }

    /**
     * Render element.
     *
     * @return string
     */
    public function render()
    {
        if ($this->_value === null) {
            $this->_value = $this->getForm()->getValue($this->getName());
        }

        $attributes = $this->getAttributes();
        $buttonTitle = 'Select file';
        if (isset($attributes['title'])) {
            $buttonTitle = $attributes['title'];
            unset($attributes['title']);
        }

        return sprintf('
            <div class="form_element_remote_file">
                <input type="text" name="%s" id="%s" value="%s" />
                <input onclick="PE.ajaxplorer.openAjaxplorerPopup($(this).parent(), \'%s\', \'%s\');"
                       type="button"
                       class="btn btn-primary"
                       value="%s"/>
            </div>',
            $this->getName(), $this->getName(), $this->_value, $this->_editorUrl, $buttonTitle, $buttonTitle);
    }
}