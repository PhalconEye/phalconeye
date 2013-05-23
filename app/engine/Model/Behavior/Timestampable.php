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

namespace Engine\Model\Behavior;

trait Timestampable
{
    /**
     * @Column(type="string", nullable=true, column="creation_date")
     */
    public $creation_date;

    /**
     * @Column(type="string", nullable=true, column="modified_date")
     */
    public $modified_date;

    public function beforeCreate()
    {
        $this->creation_date = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->modified_date = date('Y-m-d H:i:s');
    }
}