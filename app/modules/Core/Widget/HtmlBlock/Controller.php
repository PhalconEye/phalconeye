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

namespace Core\Widget\HtmlBlock;

use Core\Form\CoreForm;
use Core\Model\Language;
use Core\Model\Settings;
use Engine\Config;
use Engine\Widget\Controller as WidgetController;

/**
 * HtmlBlock widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Header
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{
    /**
     * Main action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $this->view->title = $this->getParam('title');
        $currentLanguage = $this->session->get('language');
        $defaultLanguage = Settings::getSetting('system_default_language');

        if (!$defaultLanguage || $defaultLanguage == 'auto') {
            $defaultLanguage = Config::CONFIG_DEFAULT_LANGUAGE;
        }

        $html = $this->getParam('html_' . $currentLanguage);
        if (empty($html)) {
            // let's look at default language html
            $html = $this->getParam('html_' . $defaultLanguage);
            if (empty($html)) {
                return $this->setNoRender();
            }
        }

        $this->view->html = $html;
    }

    /**
     * Admin action for editing widget options through admin panel.
     *
     * @return CoreForm
     */
    public function adminAction()
    {
        $form = new CoreForm();
        $form->addText('title');

        // Adding additional html for language selector support.
        $languageSelectorHtml = '
                <style type="text/css">
                    form .form_elements > div{
                        min-height: auto !important;
                        padding-top: 0px !important;
                    }

                    form .form_elements > div:nth-last-child(2){
                        padding-top: 10px !important;
                    }
                </style>
                <script type="text/javascript">
                     var defaultLanguage = "%s";
                     $(document).ready(function(){
                        $("#html_block_language").change(function(){
                            $("#cke_html_"+$(this).val()).closest(".form_element_container").show();
                            $(".cke").not("#cke_html_"+$(this).val()).closest(".form_element_container").hide();
                        });

                        // Hide inactive.
                        $("textarea").not("#html_"+defaultLanguage).closest(".form_element_container").hide();
                        setTimeout(
                            function(){
                                $(".cke").not("#cke_html_"+defaultLanguage).closest(".form_element_container").hide();
                            }, 200);
                    });
                </script>
                <div class="form_element_container" style="float: left;">
                    <div class="form_label">
                        <label for="title">%s</label>
                    </div>
                    <div class="form_element">
                        <select id="html_block_language" style="width: 120px;">
                            %s
                        </select>
                    </div>
                </div>
                ';

        // Creating languages boxes.
        $languages = Language::find();
        $languageHtmlItems = '';
        $defaultLanguage = Settings::getSetting('system_default_language', 'en');
        if ($defaultLanguage == 'auto') {
            $defaultLanguage = 'en';
        }
        $elements = [];

        foreach ($languages as $language) {
            $selectedLanguage = '';
            if ($language->language == $defaultLanguage) {
                $selectedLanguage = 'selected="selected"';
            }

            $elementName = 'html_' . $language->language;
            $elements[$elementName] = 'HTML (' . strtoupper($language->language) . ')';
            $languageHtmlItems .=
                '<option ' . $selectedLanguage . ' value=' . $language->language . '>' . $language->name . '</option>';
        }

        $languageSelectorHtml =
            sprintf(
                $languageSelectorHtml,
                $defaultLanguage,
                $this->di->get('i18n')->_('HTML block, for:'),
                $languageHtmlItems
            );

        // Adding created html to form.
        $form->addHtml(
            'html_language',
            $languageSelectorHtml
        );

        foreach ($elements as $elementName => $elementTitle) {
            $form->addCkEditor($elementName, $elementTitle);
        }

        $form->addHtml('separator');

        return $form;
    }

    /**
     * Cache this widget?
     *
     * @return bool
     */
    public function isCached()
    {
        return true;
    }
}