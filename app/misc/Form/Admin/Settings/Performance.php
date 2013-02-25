<?php
class Form_Admin_Settings_Performance extends Form
{

    public function init()
    {
        $this
            ->setOption('title', "Performance settings");

        $this->addElement('textField', 'prefix', array(
            'label' => 'Cache prefix',
            'description' => 'Example "pe_"',
            'value' => "pe_"
        ));

        $this->addElement('textField', 'lifetime', array(
            'label' => 'Cache lifetime',
            'description' => 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current.',
            'filter' => 'int',
            'value' => 86400
        ));

        $this->addElement('selectStatic', 'adapter', array(
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

        $this->addElement('textField', 'cacheDir', array(
            'label' => 'Files location',
            'value' => 'path_to_dir'
        ));


        /**
         * Memcached options
         */

        $this->addElement('textField', 'host', array(
            'label' => 'Memcached host',
            'value' => '127.0.0.1'
        ));

        $this->addElement('textField', 'port', array(
            'label' => 'Memcached port',
            'value' => '11211'
        ));

        $this->addElement('checkField', 'persistent', array(
            'label' => 'Create a persitent connection to memcached?',
            'options' => 1
        ));

        /**
         * Mongo options
         */

        $this->addElement('textField', 'server', array(
            'label' => 'A MongoDB connection string',
            'value' => 'mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]]'
        ));

        $this->addElement('textField', 'db', array(
            'label' => 'Mongo database name',
            'value' => 'database'
        ));

        $this->addElement('textField', 'collection', array(
            'label' => 'Mongo collection in the database',
            'value' => 'collection'
        ));

        $this->addElement('checkField', 'clear_cache', array(
            'label' => 'Clear cache',
            'description' => 'All system cache will be cleared.',
            'options' => 1
        ));


        $this->addButton('Save', true);
    }

    /**
     * @param \Phalcon\HTTP\RequestInterface $request
     *
     * @return bool
     */
    public function isValid($request){
        $adapter = $request->get('adapter');
        if ($adapter == '0'){
            $filepath = $request->get('cacheDir');
            if (!is_dir($filepath)){
                $this->addError('Files location isn\'t correct!');
                return false;
            }
        }

        return parent::isValid($request);
    }
}