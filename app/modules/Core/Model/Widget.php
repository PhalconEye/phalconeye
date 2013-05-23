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

namespace Core\Model;

/**
 * @Source("widgets")
 * @HasMany("id", '\Core\Model\Content', "widget_id", {
 *  "alias": "Content"
 * })
 */
class Widget extends \Engine\Model
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id")
     */
    public $id;

    /**
     * @Column(type="string", nullable=true, column="module")
     */
    public $module = null;

    /**
     * @Column(type="string", nullable=false, column="name")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="description")
     */
    public $description;

    /**
     * @Column(type="boolean", nullable=false, column="is_paginated")
     */
    public $is_paginated = false;

    /**
     * @Column(type="integer", nullable=false, column="is_acl_controlled")
     */
    public $is_acl_controlled = false;

    /**
     * @Column(type="string", nullable=true, column="admin_form")
     */
    public $admin_form = 'action';

    /**
     * @Column(type="boolean", nullable=false, column="enabled")
     */
    public $enabled = true;

    /**
     * Return the related "Content"
     *
     * @return \Core\Model\Content[]
     */
    public function getContent($arguments = array()){
        return $this->getRelated('Content', $arguments);
    }

    protected function beforeDelete(){
        $flag = true;
        foreach ($this->getContent() as $item) {
            $flag = $item->delete();
            if (!$flag) break;
        }
        return $flag;
    }
}
