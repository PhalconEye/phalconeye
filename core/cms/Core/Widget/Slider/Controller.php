<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Widget\Slider;

use Core\Form\CoreForm;
use Engine\Widget\Controller as WidgetController;

/**
 * Slider widget controller.
 *
 * @category  PhalconEye
 * @package   Core\Widget\Slider
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{
    const
        /**
         * Minimum number of slides
         */
        MIN_SLIDES = 3,

        /**
         * Maximum number of slides
         */
        MAX_SLIDES = 99,

        /**
         * Default duration between slides (ms)
         */
        DEFAULT_DURATION = 5000,

        /**
         * Default spped of slides  (ms)
         */
        DEFAULT_SPEED = 500,

        /**
         * Slider Css file
         */
        ASSET_CSS_URL = 'libs/bxslider-4/jquery.bxslider.css',

        /**
         * Slider Js file
         */
        ASSET_JS_URL = 'libs/bxslider-4/jquery.bxslider.min.js',

        /**
         * Slider Js video handler file
         */
        ASSET_JS_VIDEO_URL = 'libs/bxslider-4/plugins/jquery.fitvids.js';

    /**
     * Get js assets.
     *
     * @return array
     */
    public function getJsAssets()
    {
        $result = [static::ASSET_JS_URL];

        $hasVideo = (int) $this->getParam('video', 0);
        if ($hasVideo) {
            $result[] = static::ASSET_JS_VIDEO_URL;
        }

        return $result;
    }

    /**
     * Get css assets.
     *
     * @return array
     */
    public function getCssAssets()
    {
        return [static::ASSET_CSS_URL];
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $config = $this->getDI()->getConfig();

        // Slider params
        $sliderParams = [
            'duration'   => (int) $this->getParam('duration', static::DEFAULT_DURATION),
            'speed'      => (int) $this->getParam('speed', static::DEFAULT_SPEED),
            'auto'       => (int) $this->getParam('auto', 1),
            'auto_hover' => (int) $this->getParam('auto_hover', 1),
            'controls'   => (int) $this->getParam('controls', 1),
            'video'      => (int) $this->getParam('video', 0),
            'pager'      => (int) $this->getParam('pager', 1)
        ];

        // View parameters
        $this->view->baseUrl   = $config->application->baseUrl;
        $this->view->title     = (string) $this->getParam('title');
        $this->view->height    = (int) $this->getParam('height');
        $this->view->slider_id = (int) $this->getParam('content_id');
        $this->view->slides    = $this->getParam('slides', []);
        $this->view->params    = $sliderParams;
    }

    /**
     * Action for management from admin panel.
     *
     * @return CoreForm
     */
    public function adminAction()
    {
        $form = new CoreForm();

        // Dynamic slides
        $form->addContentFieldSet('Slides')
            ->addCkEditor(
                "slides[]",
                "Slides",
                '',
                [
                    'toolbar' => [['Source', '-', 'Bold', 'Italic', '-', 'Link', 'Image']],
                    'allowedContent' => true
                ],
                null,
                [
                    'dynamic' => ['min' => static::MIN_SLIDES, 'max' => static::MAX_SLIDES]
                ]
            );

        // Advanced params
        $form->addContentFieldSet('Advanced')
            ->addText('height', 'Height', 'Force height of the slider (in px)')
            ->addText('duration', 'Duration', 'Duration between slides (in ms)', static::DEFAULT_DURATION)
            ->addText('speed', 'Speed', 'Spped of slides (in ms)', static::DEFAULT_SPEED)
            ->addCheckbox('auto', 'Auto', 'Slides will automatically transition', 1, true)
            ->addCheckbox('auto_hover', 'Auto Hover', 'Auto show will pause when mouse hovers over slider', 1, true)
            ->addCheckbox('controls', 'Controls', 'Next and Prev controls will be added', 1, true)
            ->addCheckbox('video', 'Has video', 'You will need this if any slides contain video', 1, false)
            ->addCheckbox('pager', 'Pager', 'Pager will be added', 1, true);

        $form->addHtml('separator');

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminForm()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function isCached()
    {
        return true;
    }

    /**
     * Is widget display controlled by ACL?
     *
     * @return bool
     */
    public function isAclControlled()
    {
        return true;
    }
}