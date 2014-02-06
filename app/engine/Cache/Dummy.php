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

namespace Engine\Cache;

use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;

/**
 * Dummy cache.
 *
 * @category  PhalconEye
 * @package   Engine\Cache
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @SuppressWarnings(PHPMD)
 */
class Dummy extends Backend implements BackendInterface
{
    /**
     * Backend constructor.
     *
     * @param \Phalcon\Cache\FrontendInterface $frontend Frontend cache object.
     * @param array                            $options  Cache options.
     */
    public function __construct($frontend, $options = null)
    {

    }

    /**
     * Starts a cache. The $keyname allows to identify the created fragment.
     *
     * @param int|string $keyName  Key naming.
     * @param double     $lifetime Cache lifetime.
     *
     * @return null
     */
    public function start($keyName, $lifetime = null)
    {
        return null;
    }

    /**
     * Stops the frontend without store any cached content.
     *
     * @param boolean $stopBuffer Stop buffering?
     *
     * @return void
     */
    public function stop($stopBuffer = null)
    {

    }

    /**
     * Returns front-end instance adapter related to the back-end.
     *
     * @return null
     */
    public function getFrontend()
    {
        return null;
    }

    /**
     * Returns the backend options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * Checks whether the last cache is fresh or cached.
     *
     * @return boolean
     */
    public function isFresh()
    {
        return true;
    }

    /**
     * Checks whether the cache has starting buffering or not.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Sets the last key used in the cache.
     *
     * @param string $lastKey Last key name.
     *
     * @return void
     */
    public function setLastKey($lastKey)
    {
    }

    /**
     * Gets the last key stored by the cache.
     *
     * @return string
     */
    public function getLastKey()
    {
        return '';
    }

    /**
     * Returns a cached content.
     *
     * @param int|string $keyName  Key naming.
     * @param double     $lifetime Cache lifetime.
     *
     * @return  mixed
     */
    public function get($keyName, $lifetime = null)
    {
        return null;
    }

    /**
     * Stores cached content into the file backend and stops the frontend.
     *
     * @param int|string $keyName    Key naming.
     * @param string     $content    Content data.
     * @param double     $lifetime   Cache lifetime.
     * @param boolean    $stopBuffer Stop buffering?
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = null)
    {

    }

    /**
     * Deletes a value from the cache by its key.
     *
     * @param int|string $keyName Key naming.
     *
     * @return boolean
     */
    public function delete($keyName)
    {
        return false;
    }

    /**
     * Query the existing cached keys.
     *
     * @param string $prefix Keys prefix name.
     *
     * @return array
     */
    public function queryKeys($prefix = null)
    {
        return [];
    }

    /**
     * Checks if cache exists and it hasn't expired.
     *
     * @param string $keyName  Key naming.
     * @param double $lifetime Cache lifetime.
     *
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        return false;
    }

    /**
     * Increment cache.
     *
     * @param string $key_name Cache key.
     * @param mixed  $value    Cache value.
     *
     * @return void
     */
    public function increment($key_name = null, $value = null)
    {

    }

    /**
     * Decrement cache.
     *
     * @param string $key_name Cache key.
     * @param mixed  $value    Cache value.
     *
     * @return void
     */
    public function decrement($key_name = null, $value = null)
    {

    }

    /**
     * Flash the cache.
     *
     * @return void
     */
    public function flush()
    {

    }
}