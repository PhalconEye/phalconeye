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
 * @Source("menus")
 * @HasMany("id", '\Core\Model\MenuItem', "menu_id", {
 *  "alias": "MenuItem"
 * })
 */
class Menu extends \Engine\Db\Model
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name", size="255")
     */
    public $name;

    /**
     * Return the related "MenuItem"
     *
     * @return \Core\Model\MenuItem[]
     */
    public function getMenuItems($arguments = array())
    {
        return $this->getRelated('MenuItem', $arguments);
    }

    public function beforeDelete()
    {
        $this->getMenuItems()->delete();
    }
}
