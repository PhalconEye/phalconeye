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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Core\Form\Admin\Setting;

class Performance extends \Engine\Form
{

    public function init()
    {
        $this
            ->setOption('title', "Performance settings");

        $this->addElement('text', 'prefix', array(
            'label' => 'Cache prefix',
            'description' => 'Example "pe_"',
            'value' => "pe_"
        ));

        $this->addElement('text', 'lifetime', array(
            'label' => 'Cache lifetime',
            'description' => 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current.',
            'filter' => 'int',
            'value' => 86400
        ));

        $this->addElement('select', 'adapter', array(
            'label' => 'Cache adapter',
            'description' => 'Cache type. Where cache will be stored.',
            'options' => array(
                0 => 'File',
                1 => 'Memcached',
                2 => 'APC',
                3 => 'Mongo'
            ),
            'value' => 0
        ));

        /**
         * File options
         */

        $this->addElement('text', 'cacheDir', array(
            'label' => 'Files location',
            'value' => 'path_to_dir'
        ));


        /**
         * Memcached options
         */

        $this->addElement('text', 'host', array(
            'label' => 'Memcached host',
            'value' => '127.0.0.1'
        ));

        $this->addElement('text', 'port', array(
            'label' => 'Memcached port',
            'value' => '11211'
        ));

        $this->addElement('check', 'persistent', array(
            'label' => 'Create a persitent connection to memcached?',
            'options' => 1,
            'value' => true
        ));

        /**
         * Mongo options
         */

        $this->addElement('text', 'server', array(
            'label' => 'A MongoDB connection string',
            'value' => 'mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]]'
        ));

        $this->addElement('text', 'db', array(
            'label' => 'Mongo database name',
            'value' => 'database'
        ));

        $this->addElement('text', 'collection', array(
            'label' => 'Mongo collection in the database',
            'value' => 'collection'
        ));

        $this->addElement('check', 'clear_cache', array(
            'label' => 'Clear cache',
            'description' => 'All system cache will be cleared.',
            'options' => 1
        ));


        $this->addButton('Save', true);
    }

    public function isValid($data = null, $entity = null, $skipEntityCreation = false){
        if (isset($data['adapter']) && $data['adapter'] == '0'){
            if (empty($data['cacheDir']) || !is_dir($data['cacheDir'])){
                $this->addError('Files location isn\'t correct!');
                return false;
            }
        }

        return parent::isValid($data);
    }
}