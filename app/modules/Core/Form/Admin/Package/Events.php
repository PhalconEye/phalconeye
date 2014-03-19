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

namespace Core\Form\Admin\Package;

use Core\Form\CoreForm;

/**
 * Package events form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Events extends CoreForm
{
    /**
     * Package type.
     *
     * @var string
     */
    protected $_type;

    /**
     * Back link.
     *
     * @var string
     */
    protected $_link;

    /**
     * Control data.
     *
     * @var string
     */
    protected $_eventsData;

    /**
     * Create form.
     *
     * @param array  $eventsData Control data.
     * @param string $type       Package type.
     * @param string $link       Back link.
     */
    public function __construct($eventsData, $type, $link = 'admin-packages')
    {
        $this->_eventsData = $eventsData;
        $this->_type = $type;
        $this->_link = $link;
        parent::__construct();
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setAttribute('id', 'package_events_form')
            ->setTitle('Edit Package Events')
            ->setDescription(
                '
                Edit events.<br/>
                In "Event" field write event name. Example: dispatch:beforeDispatchLoop<br/>
                In "Class" field write plugin class with namespace.
                Example: Plugin\SomePlugin\Some
                '
            );

        $this->addContentFieldSet()
            ->addHtml('control', 'AdminPackages/partial/eventsControl', $this->_eventsData);

        $this->addFooterFieldSet()
            ->addButton('save')
            ->addButtonLink('cancel', 'Cancel', ['for' => $this->_link]);
    }

    /**
     * Validates the form.
     *
     * @return boolean
     */
    public function isEventsDataValid()
    {
        $data = $this->getDI()->getRequest()->getPost();

        // Check empty fields and existing files.
        $this->_eventsData = [];
        for ($i = 0, $l = count($data['event']); $i < $l; $i++) {
            if (empty($data['event'][$i]) || empty($data['class'][$i])) {
                $this->addError('Some fields are empty!');
                return false;
            }

            if (!class_exists($data['class'][$i])) {
                $this->addError('Class "' . $data['class'][$i] . '" not found.');
                return false;
            }
            $this->_eventsData[] = $data['class'][$i] . '=' . $data['event'][$i];
        }

        return true;
    }

    /**
     * Get events data.
     *
     * @return array
     */
    public function getEventsData()
    {
        return $this->_eventsData;
    }
}