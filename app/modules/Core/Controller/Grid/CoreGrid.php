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

namespace Core\Controller\Grid;

use Engine\Form;
use Engine\Grid\AbstractGrid;
use Engine\Behaviour\ViewBehaviour;
use Phalcon\Mvc\View;

/**
 * Core grid.
 * It defines base grid views.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class CoreGrid extends AbstractGrid
{
    use ViewBehaviour;

    /**
     * Get grid view name.
     *
     * @return string
     */
    public function getLayoutView()
    {
        return $this->resolveView('partials/grid/layout', 'core');
    }

    /**
     * Get grid item view name.
     *
     * @return string
     */
    public function getItemView()
    {
        return $this->resolveView('partials/grid/item', 'core');
    }

    /**
     * Get grid table body view name.
     *
     * @return string
     */
    public function getTableBodyView()
    {
        return $this->resolveView('partials/grid/body', 'core');
    }
}