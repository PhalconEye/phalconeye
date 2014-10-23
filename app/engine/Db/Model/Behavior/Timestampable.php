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

namespace Engine\Db\Model\Behavior;

/**
 * Timestampable behaviour.
 *
 * @category  PhalconEye
 * @package   Engine\Db\Model\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait Timestampable
{
    /**
     * @Column(type="datetime", nullable=true, column="creation_date")
     */
    public $creation_date;

    /**
     * @Column(type="datetime", nullable=true, column="modified_date")
     */
    public $modified_date;

    /**
     * Set creation date.
     *
     * @return void
     */
    public function beforeCreate()
    {
        $this->creation_date = date('Y-m-d H:i:s');
    }

    /**
     * Set modified date.
     *
     * @return void
     */
    public function beforeUpdate()
    {
        $this->modified_date = date('Y-m-d H:i:s');
    }
}