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

namespace Core\Helper;

use Engine\Helper;
use Phalcon\Tag;

/**
 * Javascript translator helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class I18n extends Helper
{
    /**
     * Translations.
     *
     * @var array
     */
    private $_translations = [];

    /**
     * Add translations to temporary storage.
     *
     * @param array|string $translations Translations that must be converted.
     * @param array        $params       Concatenation params.
     *
     * @return $this
     */
    public function add($translations, $params = [])
    {
        if (!is_array($translations)) {
            $translations = [$translations => $params];
        }

        $this->_translations += $translations;
        return $this;
    }

    /**
     * Output javascript translation scope.
     *
     * @param array|string $translations Translations that must be converted.
     * @param array        $params       Concatenation params.
     *
     * @return string
     */
    public function js($translations, $params = [])
    {
        if (!is_array($translations)) {
            $translations = [$translations => $params];
        }

        return $this->_render($translations);
    }

    /**
     * Clear stored translations.
     *
     * @return void
     */
    public function clear()
    {
        $this->_translations = [];
    }

    /**
     * Render current translations.
     *
     * @param array $translations Translation to render.
     *
     * @return string
     */
    public function render($translations = null)
    {
        if (!$translations) {
            $translations = $this->_translations;
        }

        $content = 'var translatorData = translatorData || [];' . PHP_EOL;
        foreach ($translations as $text => $params) {
            $content .=
                'translatorData["' . $text . '"] = "' .
                $this->getDI()->get('i18n')->query($text, $params) . '";' . PHP_EOL;
        }

        return $content;
    }
}