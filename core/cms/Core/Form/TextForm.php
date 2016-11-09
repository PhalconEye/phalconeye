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

namespace Core\Form;

/**
 * Main text form.
 *
 * @category  PhalconEye
 * @package   Core\Form
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class TextForm extends CoreForm
{
    const
        /**
         * Default layout path.
         */
        LAYOUT_TEXT_PATH = 'partials/form/text';

    use EntityForm;

    /**
     * Get layout view path.
     *
     * @return string
     */
    public function getLayoutView()
    {
        return $this->resolveView(self::LAYOUT_TEXT_PATH, 'Core');
    }
}