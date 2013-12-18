<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

use Core\Model\Package;
use Engine\Db\AbstractModel;

/**
 * Edit package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Edit extends Create
{
    /**
     * Back link.
     *
     * @var string
     */
    protected $_link;

    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     * @param string             $link  Back link.
     */
    public function __construct($model = null, $link = 'admin-packages')
    {
        $this->_link = $link;

        if ($model === null) {
            $model = new Package();
        }

        parent::__construct($model);
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this
            ->setOption('title', "Edit Package")
            ->setOption('description', "Edit this package.");

        $this->removeElement('name');
        $this->removeElement('type');
        $this->removeElement('header');

        $this->clearButtons();
        $this->addButton('Save', true);
        $this->addButtonLink('Cancel', ['for' => $this->_link]);
    }
}