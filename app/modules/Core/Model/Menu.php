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
 * Menu.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("menus")
 * @HasMany("id", '\Core\Model\MenuItem', "menu_id", {
 *  "alias": "MenuItem"
 * })
 *
 * @method static \Core\Model\Menu findFirst($parameters = null)
 */
class Menu extends AbstractModel
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
     * Return the related "MenuItem" entity.
     *
     * @param array $arguments Entity params.
     *
     * @return MenuItem[]
     */
    public function getMenuItems($arguments = [])
    {
        return $this->getRelated('MenuItem', $arguments);
    }

    /**
     * Logic before removal
     *
     * @return void
     */
    public function beforeDelete()
    {
        $this->getMenuItems()->delete();
    }
}
