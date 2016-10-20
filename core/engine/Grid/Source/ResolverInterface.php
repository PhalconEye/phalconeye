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

namespace Engine\Grid\Source;

use Engine\Form;
use Engine\Grid\GridInterface;
use Phalcon\Paginator\AdapterInterface;

/**
 * Source resolver interface.
 *
 * @category  PhalconEye
 * @package   Engine\Grid
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface ResolverInterface
{
    /**
     * Create resolver.
     *
     * @param GridInterface $grid Grid object.
     */
    public function __construct(GridInterface $grid);

    /**
     * Resolve source and return paginator.
     *
     * @param mixed $source Source.
     *
     * @return AdapterInterface
     */
    public function resolve($source);
}