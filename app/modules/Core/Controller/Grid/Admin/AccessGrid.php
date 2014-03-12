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

namespace Core\Controller\Grid\Admin;

use Core\Controller\Grid\CoreGrid;
use Engine\Form;
use Engine\Grid\GridItem;
use Engine\Grid\Source\ArrayResolver;
use Engine\Grid\Source\ResolverInterface;
use Phalcon\Mvc\View;

/**
 * Access grid.
 *
 * @category  PhalconEye
 * @package   Core\Controller\Grid\Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AccessGrid extends CoreGrid
{
    /**
     * Get main data source.
     *
     * @return array
     */
    public function getSource()
    {
        $coreApi = $this->getDI()->get('core');
        $resources = $coreApi->acl()->getResources();
        $objects = [];

        $allActions = [];
        $allObjects = [];

        foreach ($resources as $resource) {

            $object = $coreApi->acl()->getObject($resource->getName());
            if ($object == null) {
                continue;
            }

            $allActions = array_merge($allActions, $object->actions, $object->options);
            $allObjects[] = str_replace('\\', '\\\\', $resource->getName());

            $object->actions = implode(', ', $object->actions);
            $object->options = implode(', ', $object->options);

            $objects[] = (array)$object;

        }

        // Cleanup.
        // Remove unused actions and options.
        $this->getDI()->get('modelsManager')->executeQuery(
            "DELETE FROM Core\\Model\\Access WHERE action NOT IN ('" .
            implode("', '", $allActions) .
            "') OR object NOT IN ('" .
            implode("', '", $allObjects) . "')"
        );

        return $objects;
    }

    /**
     * Get source resolver.
     *
     * @return ResolverInterface
     */
    public function getSourceResolver()
    {
        return new ArrayResolver($this);
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param GridItem $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        return [
            'Edit' => ['href' => ['for' => 'admin-access-edit', 'id' => str_replace('\\', '_', $item['name'])]]
        ];
    }

    /**
     * Initialize grid columns.
     *
     * @return array
     */
    protected function _initColumns()
    {
        $this
            ->addTextColumn('name', 'Resource name')
            ->addTextColumn('module', 'Module')
            ->addTextColumn('actions', 'Actions')
            ->addTextColumn('options', 'Options');
    }
}