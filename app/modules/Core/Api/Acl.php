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

namespace Core\Api;

use Core\Model\Access;
use Engine\Api\AbstractApi;
use Engine\Application;
use Engine\DependencyInjection;
use Phalcon\Acl\Resource as AclResource;
use Phalcon\Acl as PhalconAcl;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\DI;
use Phalcon\Events\Event as PhalconEvent;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Text;
use User\Model\Role;
use User\Model\User;

/**
 * Core API Acl.
 *
 * @category  PhalconEye
 * @package   Core\Api
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Acl extends AbstractApi
{
    const
        /**
         * Acl cache key.
         */
        ACL_CACHE_KEY = "acl_data.cache";

    const
        /**
         * Role - ADMIN.
         */
        DEFAULT_ROLE_ADMIN = 'admin',

        /**
         * Role - USER.
         */
        DEFAULT_ROLE_USER = 'user',

        /**
         * Role - GUEST.
         */
        DEFAULT_ROLE_GUEST = 'guest';

    const
        /**
         * Admin area name in ACL.
         */
        ACL_ADMIN_AREA = 'AdminArea';

    /**
     * Acl adapter.
     *
     * @var AclMemory
     */
    protected $_acl;

    /**
     * Get acl system.
     *
     * @return AclMemory
     */
    public function _()
    {
        if (!$this->_acl) {
            $cacheData = $this->getDI()->get('cacheData');
            $acl = $cacheData->get(self::ACL_CACHE_KEY);
            if ($acl === null) {
                $acl = new AclMemory();
                $acl->setDefaultAction(PhalconAcl::DENY);

                // Prepare Roles.
                $roles = Role::find();
                $roleNames = [];
                foreach ($roles as $role) {
                    $roleNames[$role->id] = $role->name;
                    $acl->addRole($role->name);
                }

                // Defining admin area.
                $adminArea = new AclResource(self::ACL_ADMIN_AREA);
                $roleAdmin = Role::getRoleByType(self::DEFAULT_ROLE_ADMIN);

                // Add "admin area" resource.
                $acl->addResource($adminArea, "access");
                $acl->allow($roleAdmin->name, self::ACL_ADMIN_AREA, 'access');


                // Getting objects that is in acl.
                // Looking for all models in modelsDir and check @Acl annotation.
                $objects = [self::ACL_ADMIN_AREA => ['actions' => ['access']]];
                $this->_addResources($acl, $objects);

                // Load from database.
                $access = Access::find();
                foreach ($access as $item) {

                    $value = $item->value;

                    if (
                        array_key_exists($item->object, $objects) &&
                        in_array($item->action, $objects[$item->object]['actions']) &&
                        ($value == "allow" || $value == "deny")
                    ) {
                        $acl->$value($roleNames[$item->role_id], $item->object, $item->action);
                    }
                }
                $cacheData->save(self::ACL_CACHE_KEY, $acl, 2592000); // 30 days cache.
            }
            $this->_acl = $acl;
        }

        return $this->_acl;
    }

    /**
     * Get allowed value.
     *
     * @param string $objectName Object name.
     * @param Role   $role       Role object.
     * @param string $action     Action name.
     *
     * @return null|mixed
     */
    public function getAllowedValue($objectName, Role $role, $action)
    {
        $result = Access::findFirst(
            [
                "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                "bind" => [1 => $objectName, 2 => $action, 3 => $role->id]
            ]
        );

        if ($result) {
            return $result->value;
        }

        return null;
    }

    /**
     * Get acl object.
     *
     * @param string $objectName Object name.
     *
     * @return null|\stdClass
     */
    public function getObjectAcl($objectName)
    {
        $object = new \stdClass();
        $object->name = $objectName;
        $object->actions = [];
        $object->options = [];

        if ($objectName == self::ACL_ADMIN_AREA) {
            $object->actions = ['access'];

            return $object;
        }

        $reader = new \Phalcon\Annotations\Adapter\Memory();
        $reflector = $reader->get($objectName);
        $annotations = $reflector->getClassAnnotations();
        if ($annotations && $annotations->has('Acl')) {
            $annotation = $annotations->get('Acl');

            if ($annotation->hasNamedArgument('actions')) {
                $object->actions = $annotation->getNamedArgument('actions');
            }
            if ($annotation->hasNamedArgument('options')) {
                $object->options = $annotation->getNamedArgument('options');
            }
        } else {
            return null;
        }

        return $object;
    }

    /**
     * Clear acl cache.
     *
     * @return void
     */
    public function clearAcl()
    {
        $this->getDI()->get('cacheData')->delete(self::ACL_CACHE_KEY);
    }

    /**
     * This action is executed before execute any action in the application.
     *
     * @param PhalconEvent $event      Event object.
     * @param Dispatcher   $dispatcher Dispatcher object.
     *
     * @return mixed
     */
    public function beforeDispatch(PhalconEvent $event, Dispatcher $dispatcher)
    {
        $viewer = User::getViewer();
        $acl = $this->_();

        $controller = $dispatcher->getControllerName();

        // Check admin area.
        if (
            Text::startsWith($controller, 'Admin', true) &&
            $acl->isAllowed($viewer->getRole()->name, self::ACL_ADMIN_AREA, 'access') != PhalconAcl::ALLOW
        ) {
            return $dispatcher->forward(
                [
                    'module' => Application::SYSTEM_DEFAULT_MODULE,
                    'namespace' => ucfirst(Application::SYSTEM_DEFAULT_MODULE) . '\Controller',
                    "controller" => 'Error',
                    "action" => 'show404'
                ]
            );

        }

        return !$event->isStopped();
    }

    /**
     * Add resources to acl.
     *
     * @param AclMemory $acl     Acl object.
     * @param array     $objects Related objects collection.
     */
    protected function _addResources($acl, $objects)
    {
        $config = $this->getDI()->get('config');
        foreach ($this->getDI()->get('modules') as $module) {
            $module = ucfirst($module);
            $modelsPath = $config->directories->modules . $module . '/Model';
            if (file_exists($modelsPath)) {
                $files = scandir($modelsPath);
                foreach ($files as $file) {
                    if ($file == "." || $file == "..") {
                        continue;
                    }
                    $class = sprintf('\%s\Model\%s', $module, ucfirst(str_replace('.php', '', $file)));
                    $object = $this->getObjectAcl($class);
                    if ($object == null) {
                        continue;
                    }

                    $objects[$class]['actions'] = $object->actions;
                    $objects[$class]['options'] = $object->options;
                }

                // Add objects to resources.
                foreach ($objects as $key => $object) {
                    if (empty($object['actions'])) {
                        $object['actions'] = [];
                    }
                    $acl->addResource($key, $object['actions']);
                }
            }
        }
    }
}