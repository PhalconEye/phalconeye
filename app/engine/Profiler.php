<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

class Profiler
{
    protected $_dbProfiler = null;
    protected $_time = null;
    protected $_memory = null;
    protected $_timeData = array();
    protected $_memoryData = array();
    protected $_errorData = array();
    public static $objectTypes = array(
        'controller',
        'widget',
        'view',
        'form',
        'helper'
    );

    /**
     * Start profiling
     */
    public function start()
    {
        $this->_time = microtime(true);
        $this->_memory = memory_get_usage();
    }

    /**
     * Stop profiling and collect data
     *
     * @param $class
     * @param $objectType
     * @param null $object
     */
    public function stop($class, $objectType, $object = null)
    {
        if (!isset($this->_timeData[$objectType])) {
            $this->_timeData[$objectType] = array();
        }
        $this->_timeData[$objectType][$class] = microtime(true) - $this->_time;

        $memory = memory_get_usage() - $this->_memory;
        if ($object) {
            $memory = $this->getObjectMemoryUsage($object);
        }
        if (!isset($this->_memoryData[$objectType])) {
            $this->_memoryData[$objectType] = array();
        }
        $this->_memoryData[$objectType][$class] = $memory;
    }

    /**
     * Get collected data
     *
     * @param $type - profiling type (time, memory, etc)
     * @param $objectType - object type (controller, widget, etc)
     * @return array
     */
    public function getData($type, $objectType = null){
        $var = "_{$type}Data";
        $data = $this->$var;

        if (!$objectType){
            return $data;
        }

        if (empty($data[$objectType])){
            return array();
        }

        return $data[$objectType];
    }


    /**
     * Collect errors
     *
     * @param $error
     * @param $trace
     */
    public function addError($error, $trace){
        $this->_errorData[] = array(
            'error' => $error,
            'trace' => $trace
        );
    }

    /**
     * Set Phalcon database profiler
     *
     * @param \Phalcon\Db\Profiler $profiler
     */
    public function setDbProfiler($profiler)
    {
        $this->_dbProfiler = $profiler;
    }

    /**
     * Get Phalcon database profiler
     *
     * @return \Phalcon\Db\Profiler null
     */
    public function getDbProfiler()
    {
        return $this->_dbProfiler;
    }

    /**
     * Get object memory usage
     *
     * @param $object
     * @return int
     */
    public function getObjectMemoryUsage($object)
    {
        $memory = 0;
        if (!is_scalar($object)) {
            while (list($prop, $propVal) = each($object)) {
                if ((is_object($propVal) || is_array($propVal))) {
                    $memory += $this->getObjectMemoryUsage($propVal);
                } else {
                    if (is_object($propVal) && get_class($propVal) == 'Closure') {
                        $serializableClosure = new \SerializableClosure($propVal);
                        $memory += strlen($serializableClosure->serialize());
                    } else {
                        $memory += strlen(serialize($propVal));
                    }
                }
            }
        } else {
            $memory += strlen(serialize($object));
        }

        return $memory;
    }
}