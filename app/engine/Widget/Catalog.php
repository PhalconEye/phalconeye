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
 * @copyright 2013-2014 PhalconEye Team
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
     * Widgets identities.
     *
     * @var array
     */
    protected $_ids = [];

    /**
     * Widgets keys.
     *
     * @var array
     */
    protected $_keys = [];

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
     * @param int    $id     Widget identity.
     * @param string $key    Widget unique key.
     * @param mixed  $widget Widget model.
     *
     * @return void
     * @throws EngineException
     */
    public function addWidget($id, $key, $widget)
    {
        if (isset($this->_ids[$id])) {
            throw new EngineException(sprintf('Widget storage has already widget with id "%s".', $id));
        }

        if (isset($this->_keys[$key])) {
            throw new EngineException(sprintf('Widget storage has already widget with key "%s".', $key));
        }

        $index = count($this->_widgets);
        $this->_widgets[] = $widget;
        $this->_ids[$id] = $index;
        $this->_keys[$key] = $index;
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
        foreach ($widgets as $widget) {
            $this->addWidget($widget[0], $widget[1], $widget[2]);
        }
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
        if (is_int($id)) {
            if (!isset($this->_ids[$id])) {
                throw new EngineException(sprintf('Widget storage has no widget with id "%s".', $id));
            }
            $index = $this->_ids[$id];
        } else {
            if (!isset($this->_keys[$id])) {
                throw new EngineException(sprintf('Widget storage has no widget with key "%s".', $id));
            }
            $index = $this->_keys[$id];
        }

        return $this->_widgets[$index];
    }
}