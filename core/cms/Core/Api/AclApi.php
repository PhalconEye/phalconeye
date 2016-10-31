<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Api;

use Core\Model\AccessModel;
use Engine\Api\AbstractApi;
use Engine\Application;
use Engine\Package\PackageManager;
use Engine\Utils\FileUtils;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Resource as AclResource;
use Phalcon\Acl as PhalconAcl;
use Phalcon\Events\Event as PhalconEvent;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Text;
use User\Model\RoleModel;
use User\Model\UserModel;

/**
 * Core API Acl.
 *
 * @category  PhalconEye
 * @package   Core\Api
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AclApi extends AbstractApi
{
    const
        /**
         * Acl cache key.
         */
        CACHE_KEY_ACL = "acl_data";

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
    public function getAcl()
    {
        if (!$this->_acl) {
            $cacheData = $this->getDI()->get('cacheData');
            $acl = $cacheData->get(self::CACHE_KEY_ACL);
            if ($acl === null) {
                $acl = new AclMemory();
                $acl->setDefaultAction(PhalconAcl::DENY);

                // Prepare Roles.
                $roles = RoleModel::find();
                $roleNames = [];
                foreach ($roles as $role) {
                    $roleNames[$role->id] = $role->name;
                    $acl->addRole($role->name);
                }

                // Defining admin area.
                $adminArea = new AclResource(self::ACL_ADMIN_AREA);
                $roleAdmin = RoleModel::getRoleByType(self::DEFAULT_ROLE_ADMIN);

                // Add "admin area" resource.
                $acl->addResource($adminArea, "access");
                $acl->allow($roleAdmin->name, self::ACL_ADMIN_AREA, 'access');

                // Getting objects that is in acl.
                // Looking for all models in modelsDir and check @Acl annotation.
                $objects = $this->_addResources($acl, [self::ACL_ADMIN_AREA => ['actions' => ['access']]]);

                // Load from database.
                $access = AccessModel::find();
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
                $cacheData->save(self::CACHE_KEY_ACL, $acl, 2592000); // 30 days cache.
            }
            $this->_acl = $acl;
        }

        return $this->_acl;
    }

    /**
     * Wrapper to real isAllowed method.
     *
     * @param string $role     Role name.
     * @param string $resource Resource name.
     * @param string $access   Access name.
     *
     * @return boolean
     */
    public function isAllowed($role, $resource, $access)
    {
        return $this->getAcl()->isAllowed($role, $resource, $access);
    }

    /**
     * Get allowed value.
     *
     * @param string    $objectName Object name.
     * @param RoleModel $role       Role object.
     * @param string    $action     Action name.
     *
     * @return null|mixed
     */
    public function getAllowedValue($objectName, RoleModel $role, $action)
    {
        $result = AccessModel::findFirst(
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
    public function getObject($objectName)
    {
        $object = new \stdClass();
        $object->name = $objectName;
        $object->module = ucfirst(Application::CMS_MODULE_CORE);
        $object->actions = [];
        $object->options = [];

        if ($objectName == self::ACL_ADMIN_AREA) {
            $object->actions = ['access'];

            return $object;
        }

        $objectNameParts = explode('\\', $objectName);
        if (count($objectNameParts) > 1) {
            $object->module = $objectNameParts[1];
        }

        $reader = new \Phalcon\Annotations\Adapter\Memory();
        $reflector = $reader->get($objectName);
        $annotations = $reflector->getClassAnnotations();
        if ($annotations && $annotations->has('Acl')) {
            $annotation = $annotations->get('Acl');

            if ($annotation->getNamedArgument('actions')) {
                $object->actions = $annotation->getNamedArgument('actions');
            }
            if ($annotation->getNamedArgument('options')) {
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
        $this->getDI()->get('cacheData')->delete(self::CACHE_KEY_ACL);
    }

    /**
     * Return an array with every resource registered in the list.
     *
     * @return \Phalcon\Acl\Resource[]
     */
    public function getResources()
    {
        return $this->getAcl()->getResources();
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
        $viewer = UserModel::getViewer();
        $acl = $this->getAcl();

        // Check admin area.
        if (
            Text::endsWith($dispatcher->getNamespaceName(), 'Backoffice', true) &&
            $acl->isAllowed($viewer->getRole()->name, self::ACL_ADMIN_AREA, 'access') != PhalconAcl::ALLOW
        ) {
            $this->getDI()->getEventsManager()->fire(
                'dispatch:beforeException',
                $dispatcher,
                new Dispatcher\Exception()
            );
        }

        return !$event->isStopped();
    }

    /**
     * Add resources to acl.
     *
     * @param AclMemory $acl     Acl object.
     * @param array     $objects Related objects collection.
     *
     * @return array
     */
    protected function _addResources($acl, $objects)
    {
        foreach ($this->getDI()->getModules()->getPackages() as $module) {
            $modelsPath = $module->getPath() . 'Model';
            if (file_exists($modelsPath)) {
                $files = FileUtils::globRecursive($modelsPath, '*.php');
                foreach ($files as $file) {
                    $modelName = str_replace([$modelsPath, DS, '.php'], ['', PackageManager::SEPARATOR_NS, ''], $file);
                    $modelClass = sprintf('\%s\Model%s', $module->getNameUpper(), ucfirst($modelName));
                    $object = $this->getObject($modelClass);
                    if ($object == null) {
                        continue;
                    }

                    $objects[$modelClass]['actions'] = $object->actions;
                    $objects[$modelClass]['options'] = $object->options;
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

        return $objects;
    }
}
