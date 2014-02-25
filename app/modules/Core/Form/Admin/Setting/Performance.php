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

namespace Core\Form\Admin\Setting;

use Core\Form\CoreForm;

/**
 * Performance settings.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Setting
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Performance extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setTitle('Performance settings');

        $this->addContentFieldSet()
            ->addText('prefix', 'Cache prefix', 'Example: "pe_"', 'pe_')
            ->addText(
                'lifetime',
                'Cache lifetime',
                'This determines how long the system will keep cached data before
                    reloading it from the database server.
                    A shorter cache lifetime causes greater database server CPU usage,
                    however the data will be more current.',
                86400
            )
            ->addSelect(
                'adapter',
                'Cache adapter',
                'Cache type. Where cache will be stored.',
                [
                    0 => 'File',
                    1 => 'Memcached',
                    2 => 'APC',
                    3 => 'Mongo'
                ],
                0
            )

            /**
             * File options
             */
            ->addText('cacheDir', 'Files location', null, ROOT_PATH . '/app/var/cache/data/')

            /**
             * Memcached options.
             */
            ->addText('host', 'Memcached host', null, '127.0.0.1')
            ->addText('port', 'Memcached port', null, '11211')
            ->addCheckbox('persistent', 'Create a persistent connection to memcached?', null, 1, true, 0)

            /**
             * Mongo options.
             */
            ->addText(
                'server',
                'A MongoDB connection string',
                null,
                'mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]]'
            )
            ->addText('db', 'Mongo database name', null, 'database')
            ->addText('collection', 'Mongo collection in the database', null, 'collection')

            /**
             * Other.
             */
            ->addCheckbox('clear_cache', 'Clear cache', 'All system cache will be cleaned.', 1, false, 0);

        $this->addFooterFieldSet()->addButton('save');

        $this->addFilter('lifetime', self::FILTER_INT);
        $this->_setConditions();
    }

    /**
     * Validates the form.
     *
     * @param array $data               Data to validate.
     * @param bool  $skipEntityCreation Skip entity creation.
     *
     * @return boolean
     */
    public function isValid($data = null, $skipEntityCreation = false)
    {
        if (!$data) {
            $data = $this->getDI()->getRequest()->getPost();
        }

        if (isset($data['adapter']) && $data['adapter'] == '0') {
            if (empty($data['cacheDir']) || !is_dir($data['cacheDir'])) {
                $this->addError('Files location isn\'t correct!');

                return false;
            }
        }

        return parent::isValid($data, $skipEntityCreation);
    }

    /**
     * Set form conditions.
     *
     * @return void
     */
    protected function _setConditions()
    {
        $content = $this->getFieldSet(self::FIELDSET_CONTENT);

        /**
         * Files conditions.
         */
        $content->setCondition('cacheDir', 'adapter', 0);

        /**
         * Memcached conditions.
         */
        $content->setCondition('host', 'adapter', 1);
        $content->setCondition('port', 'adapter', 1);
        $content->setCondition('persistent', 'adapter', 1);

        /**
         * Mongo conditions.
         */
        $content->setCondition('server', 'adapter', 3);
        $content->setCondition('db', 'adapter', 3);
        $content->setCondition('collection', 'adapter', 3);
    }
}