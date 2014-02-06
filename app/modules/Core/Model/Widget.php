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

namespace Core\Model;

use Engine\Db\AbstractModel;

/**
 * Widget.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("widgets")
 * @HasMany("id", '\Core\Model\Content', "widget_id", {
 *  "alias": "Content"
 * })
 */
class Widget extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name", size="150")
     */
    public $name;

    /**
     * @Column(type="string", nullable=true, column="module", size="64")
     */
    public $module = null;

    /**
     * @Column(type="string", nullable=true, column="description", size="255")
     */
    public $description;

    /**
     * @Column(type="boolean", nullable=false, column="is_paginated")
     */
    public $is_paginated = false;

    /**
     * @Column(type="boolean", nullable=false, column="is_acl_controlled")
     */
    public $is_acl_controlled = false;

    /**
     * @Column(type="string", nullable=true, column="admin_form", size="255")
     */
    public $admin_form = 'action';

    /**
     * @Column(type="boolean", nullable=false, column="enabled")
     */
    public $enabled = true;

    /**
     * Get widget key.
     *
     * @return string
     */
    public function getKey()
    {
        $key = $this->name;
        if (!empty($this->module)) {
            $key = ucfirst($this->module) . '.' . $key;
        }

        return $key;
    }

    /**
     * Return the related "Content" entity.
     *
     * @param array $arguments Entity arguments.
     *
     * @return Content[]
     */
    public function getContent($arguments = [])
    {
        return $this->getRelated('Content', $arguments);
    }

    /**
     * Logic before removal.
     *
     * @return bool
     */
    protected function beforeDelete()
    {
        $flag = true;
        foreach ($this->getContent() as $item) {
            $flag = $item->delete();
            if (!$flag) {
                break;
            }
        }

        return $flag;
    }
}
