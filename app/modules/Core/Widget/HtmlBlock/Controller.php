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

namespace Core\Widget\HtmlBlock;

use Core\Model\Language;
use Core\Model\Settings;
use Engine\Config;
use Engine\Form;
use Engine\Widget\Controller as WidgetController;

/**
 * HtmlBlock widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Header
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
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
     * @return Form
     */
    public function adminAction()
    {
        $form = new Form();

        $form->addElement(
            'text',
            'title',
            [
                'label' => 'Title'
            ]
        );

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
                     var defaultLocale = "%s";
                     $(document).ready(function(){
                        $("#html_block_language").change(function(){
                            $("#cke_html_"+$(this).val()).show();
                            $(".cke").not("#cke_html_"+$(this).val()).hide();
                        });

                        // hide inactive
                        $("textarea").not("#html_"+defaultLocale).hide();

                        setTimeout(function(){
                            %s
                            setTimeout(function(){$(".cke").not("#cke_html_"+defaultLocale).hide();}, 200);
                        }, 200);
                    });
                </script>
                <div style="margin-bottom: -65px;float: left;">
                    <div class="form_label">
                        <label for="title">%s</label>
                        <select id="html_block_language" style="width: 120px;">
                        %s
                        </select>
                    </div>
                </div>
                ';

        // Creating languages boxes.
        $languages = Language::find();
        $languageHtmlItems = '';
        $languageTextCode = '';
        $defaultLocale = Settings::getSetting('system_default_language', 'en');

        $order = 3; // All textarea's must be ordered together.
        foreach ($languages as $language) {
            $selectedLanguage = '';
            if ($language->locale == $defaultLocale) {
                $selectedLanguage = 'selected="selected"';
            }

            $form->addElement('textArea', 'html_' . $language->locale, [], $order++);
            $languageTextCode .= 'CKEDITOR.replace("html_' . $language->locale . '");';
            $languageHtmlItems .=
                '<option ' . $selectedLanguage . ' value=' . $language->locale . '>' . $language->name . '</option>';
        }

        $languageSelectorHtml =
            sprintf(
                $languageSelectorHtml,
                $defaultLocale,
                $languageTextCode,
                $this->di->get('trans')->_('HTML block, for:'),
                $languageHtmlItems
            );

        // Adding created html to form.
        $form->addElement(
            'html',
            'html',
            [
                'ignore' => true,
                'html' => $languageSelectorHtml
            ]
        );

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