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

namespace Engine\Helper;

use Engine\Asset\Manager;

/**
 * Assets helper
 *
 * @category  PhalconEye
 * @package   Engine\Helper
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AssetsHelper extends AbstractHelper
{
    /**
     * Add javascript file to assets.
     *
     * @param string $file       File path.
     * @param string $collection Collection name.
     *
     * @return void
     */
    public function addJs($file, $collection = Manager::DEFAULT_COLLECTION_JS)
    {
        $this->getDI()->get('assets')->get($collection)->addJs($file);
    }

    /**
     * Add css file to assets.
     *
     * @param string $file       File path.
     * @param string $collection Collection name.
     *
     * @return void
     */
    public function addCss($file, $collection = Manager::DEFAULT_COLLECTION_CSS)
    {
        $this->getDI()->get('assets')->get($collection)->addCss($file);
    }
}