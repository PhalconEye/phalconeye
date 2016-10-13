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

namespace engine\Asset;

use Engine\Behaviour\DIBehaviour;
use Phalcon\Assets\Collection as PhalconCollection;

/**
 * Assets collection.
 *
 * @category  PhalconEye
 * @package   Engine\Asset
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Collection extends PhalconCollection
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * Application config.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Initialize assets manager.
     *
     * @param DiInterface $di      Dependency injection.
     */
    public function __construct($di)
    {
        $this->__DIConstruct($di);
        $this->_config = $di->getConfig();
    }

    /**
     * Adds a CSS resource to the collection
     *
     * @param string $path
     * @param mixed $local
     * @param bool $filter
     * @param mixed $attributes
     * @return Collection
     */
    public function addCss($path, $local = null, $filter = true, $attributes = null)
    {
        $path = Manager::ASSETS_PATH . $path;

        if (!$this->_config->application->debug) {
            $path = PUBLIC_PATH . $path;
        }

        return parent::addCss($path, $local, $filter, $attributes);
    }

    /**
     * Adds a javascript resource to the collection
     *
     * @param string $path
     * @param boolean $local
     * @param boolean $filter
     * @param array $attributes
     * @return \Phalcon\Assets\Collection
     */
    public function addJs($path, $local = null, $filter = true, $attributes = null)
    {
        $path = Manager::ASSETS_PATH . $path;

        if (!$this->_config->application->debug) {
            $path = PUBLIC_PATH . $path;
        }

        return parent::addJs($path, $local, $filter, $attributes);
    }
}