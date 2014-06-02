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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Form\Element;

use Engine\Form\AbstractElement;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Form\ElementInterface;

/**
 * Form element - Remote File (Ajaxplorer seleciton mode).
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RemoteFile extends AbstractElement implements ElementInterface
{
    use TranslationBehaviour;

    const
        /**
         * Ajaxplorer url.
         */
        EDITOR_URL = 'external/pydio/?external_selector_type=popup&amp;relative_path=/files';

    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return array_merge(parent::getAllowedOptions(), ['buttonTitle']);
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption(
            'htmlTemplate',
            '
                <div class="form_element_remote_file">
                    <input type="text" name="%s" id="%s" value="%s" />
                    <input onclick="PhalconEye.pydio.openPopup($(this).parent(), \'%s\', \'%s\');"
                           type="button"
                           class="btn btn-primary"
                           value="%s"/>
                </div>
            '
        );
    }

    /**
     * Get element html template values
     *
     * @return array
     */
    public function getHtmlTemplateValues()
    {
        $buttonTitle = $this->getOption('buttonTitle');
        if (!$buttonTitle) {
            $buttonTitle = $this->_('Select file');
        }

        return [
            $this->getName(),
            $this->getAttribute('id'),
            $this->getValue(),
            $this->getDI()->getUrl()->get(self::EDITOR_URL),
            $buttonTitle,
            $buttonTitle
        ];
    }
}