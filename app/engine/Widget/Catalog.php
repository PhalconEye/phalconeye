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

namespace Engine\Widget;

use Engine\Exception as EngineException;

/**
 * Widgets catalog.
 *
 * @category  PhalconEye
 * @package   Engine\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Catalog
{
    /**
     * Widgets in storage.
     *
     * @var array
     */
    protected $_widgets = [];

    /**
     * Get widgets in storage.
     *
     * @return array
     */
    public function getWidgets()
    {
        return $this->_widgets;
    }

    /**
     * Add one widget to catalog.
     *
     * @param mixed $id     Widget identity.
     * @param mixed $widget Widget model.
     *
     * @return void
     * @throws EngineException
     */
    public function addWidget($id, $widget)
    {
        if (isset($this->_widgets[$id])) {
            throw new EngineException(sprintf('Widget storage has already widget with id "%s".', $id));
        }

        $this->_widgets[$id] = $widget;
    }

    /**
     * Insert widgets to storage.
     *
     * @param array $widgets Widgets.
     *
     * @return void
     */
    public function addWidgets($widgets)
    {
        $this->_widgets += $widgets;
    }

    /**
     * Get widget by ID.
     *
     * @param mixed $id Widget identity in storage.
     *
     * @return mixed
     * @throws EngineException
     */
    public function get($id)
    {
        if (empty($this->_widgets[$id])) {
            throw new EngineException(sprintf('Widget storage has no widget with id "%s".', $id));
        }

        return $this->_widgets[$id];
    }
}