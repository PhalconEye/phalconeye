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

namespace Engine\Behavior;

use Engine\Behavior\DIBehavior;
use Phalcon\DI;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Volt;

/**
 * View behavior.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait ViewBehavior
{
    /**
     * Is backoffice view.
     *
     * @var boolean
     */
    private $_isBackoffice = false;

    /**
     * Resolve view path.
     *
     * @param string $viewPath Path to view.
     * @param null   $module   Module name.
     *
     * @return string
     */
    public function resolveView($viewPath, $module = null)
    {
        if (!$module) {
            return $viewPath;
        }

        $backofficePath = '';
        if ($this->isBackoffice()) {
            $backofficePath = 'Backoffice/';
        }

        return $module . '/View/' . $backofficePath . $viewPath;
    }

    /**
     * Check if current view must render backoffice views.
     *
     * @return boolean
     */
    public function isBackoffice(): bool
    {
        return $this->_isBackoffice;
    }

    /**
     * Set that engine must render backoffice views.
     *
     * @param boolean $isBackoffice Is backoffice view?
     */
    public function setIsBackoffice(bool $isBackoffice)
    {
        $this->_isBackoffice = $isBackoffice;
    }
}