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

use Engine\Application;
use Engine\View;
use Phalcon\Mvc\View as PhalconView;

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
     * @param string      $viewPath Path to view.
     * @param string|null $module   Module name.
     *
     * @return string
     */
    public function resolveView($viewPath, $module = null)
    {
        if ($this->isBackoffice()) {
            $viewPath = View::PATH_BACKOFFICE . DS . $viewPath;
        }

        if (!empty($module)) {
            return $module . DS . View::PATH_VIEW . DS . $viewPath;
        }

        return $viewPath;
    }

    /**
     * Resolve view path.
     *
     * @param string $viewPath Path to view.
     * @param string $module   Module name.
     *
     * @return string
     */
    public function resolvePartial($viewPath, $module = Application::CMS_MODULE_CORE)
    {
        if (!empty($module)) {
            return $module . DS . View::PATH_VIEW . DS . $viewPath;
        }

        return $viewPath;
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