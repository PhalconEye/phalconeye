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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Backoffice\Setting;

use Core\Form\CoreForm;
use Core\Model\LanguageModel;
use Core\Model\SettingsModel;

/**
 * System settings.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Setting
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class SettingSystemForm extends CoreForm
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
            ->addText('title', 'Site name', null, SettingsModel::getValue('system', 'title', ''))
            ->addSelect('theme', 'Theme', null, $this->_getThemeOptions(), SettingsModel::getValue('system', 'theme'))
            ->addSelect(
                'default_language',
                'Default language',
                null,
                $this->_getLanguageOptions(),
                SettingsModel::getValue('system', 'default_language')
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
        foreach (LanguageModel::find() as $language) {
            $languages[$language->language] = $language->name;
        }

        return $languages;
    }
}