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

namespace Engine\Translation;

/**
 * Language translation interface.
 *
 * @category  PhalconEye
 * @package   Engine\Translation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface TranslationModelInterface
{
    /**
     * Set scope.
     *
     * @param string $scope Scope name.
     *
     * @return mixed
     */
    public function setScope($scope);

    /**
     * Set language id.
     *
     * @param int $languageId Language id.
     *
     * @return mixed
     */
    public function setLanguageId($languageId);

    /**
     * Set translation original text.
     *
     * @param string $text Original text.
     *
     * @return mixed
     */
    public function setOriginal($text);

    /**
     * Set translated text.
     *
     * @param string $text Translated text.
     *
     * @return mixed
     */
    public function setTranslated($text);

    /**
     * Get translated data.
     *
     * @return string
     */
    public function getTranslated();
}