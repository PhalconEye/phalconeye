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

namespace Engine\View\Plugin;

use Engine\Exception;
use Engine\Profiler;
use Engine\View;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * View plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ViewPlugin extends PhalconPlugin
{
    /** @var Profiler $_profiler */
    private $_profiler = null;
    private $_rendered = [];

    /**
     * ViewPlugin constructor.
     */
    public function __construct()
    {
        if ($this->getDI()->getConfig()->application->profiler && $this->getDI()->has('profiler')) {
            $this->_profiler = $this->getDI()->get('profiler');
        }
    }

    /**
     * Before render event handler.
     */
    public function beforeRender()
    {
        if ($this->_profiler) {
            $this->_profiler->start();
        }
    }

    /**
     * After render event handler.
     *
     * @param Event $event Event object.
     */
    public function afterRender($event)
    {
        if ($this->_profiler) {
            $this->_profiler->stop($event->getSource()->getActiveRenderPath(), 'view');
        }
    }

    /**
     * View not found event handler.
     *
     * @param Event $event Event object.
     *
     * @throws Exception
     */
    public function notFoundView($event)
    {
        $notFound = $event->getSource()->getActiveRenderPath();
        if (is_array($notFound)) {
            $notFound = implode(", ", $notFound);
        }

        throw new Exception('View not found - "' . $notFound . '"');
    }

    /**
     * Before render view event handler.
     *
     * @param Event $event Event object.
     * @param View  $view  View object.
     *
     * @return bool
     */
    public function beforeRenderView($event, $view)
    {
        /**
         * Do not allow rendering of same view twice.
         * Views are loading by priority: first from themes, then from modules, and the last from cms folders.
         */
        $currentView = $view->getCurrentView();
        $fullPath = $view->getCurrentPath();

        if (empty($currentView) || empty($fullPath)) {
            return true;
        }

        $location = str_replace($currentView, '', $fullPath);

        if (array_key_exists($currentView, $this->_rendered)) {
            // Check if same view is from another place - do not render it.
            $originalViewLocation = $this->_rendered[$currentView];
            if ($originalViewLocation != $location) {
                $event->stop();
                return false;
            }

            return true;
        }

        /**
         * Remember from where was rendered this view.
         */
        $this->_rendered[$currentView] = $location;
        return true;
    }
}