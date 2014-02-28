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

namespace Core\Form\Admin\Setting;

use Core\Form\CoreForm;
use Core\Model\Language;
use Core\Model\Settings;

/**
 * System settings.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Setting
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class System extends CoreForm
{
    const
        /**
         * Themes directory location.
         */
        THEMES_DIR = '/themes/';

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('System settings')
            ->setDescription('All system settings here.');

        $this->addContentFieldSet()
            ->addText('system_title', 'Site name', null, Settings::getSetting('system_title', ''))
            ->addSelect('system_theme', 'Theme', null, $this->_getThemeOptions(), Settings::getSetting('system_theme'))
            ->addSelect(
                'system_default_language',
                'Default language',
                null,
                $this->_getLanguageOptions(),
                Settings::getSetting('system_default_language')
            );


        $this->addFooterFieldSet()->addButton('save');
    }


    /**
     * Get themes options for select.
     *
     * @return array
     */
    protected function _getThemeOptions()
    {
        $themes = [];
        foreach (scandir(PUBLIC_PATH . self::THEMES_DIR) as $entry) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $themes[$entry] = ucfirst($entry);
        }

        return $themes;
    }

    /**
     * Get language options.
     *
     * @return array
     */
    protected function _getLanguageOptions()
    {
        $languages = ['auto' => 'Auto detect'];
        foreach (Language::find() as $language) {
            $languages[$language->language] = $language->name;
        }

        return $languages;
    }
}