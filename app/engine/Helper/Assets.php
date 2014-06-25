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

namespace Engine\Helper;

use Engine\Asset\Manager;
use Engine\Helper;
use Phalcon\DI;
use Phalcon\Tag;

/**
 * Assets helper
 *
 * @category  PhalconEye
 * @package   Engine\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Assets extends Helper
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