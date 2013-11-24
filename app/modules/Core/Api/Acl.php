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

namespace Core\Api;

use Engine\Api\ApiInterface;
use Phalcon\Acl as PhAcl;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Resource as AclResource;
use Phalcon\DiInterface;

class Acl implements ApiInterface
{
    const ACL_CACHE_KEY = "acl_data.cache";
    const DEFAULT_ROLE_ADMIN = 'admin';
    const DEFAULT_ROLE_USER = 'user';
    const DEFAULT_ROLE_GUEST = 'guest';

    const ACL_ADMIN_AREA = 'AdminArea';

    /**
     * @var \Phalcon\Acl\Adapter\Memory
     */
    protected $_acl;

    /**
     * @var \Phalcon\DiInterface
     */
    protected $_di;

    /**
     * @param \Phalcon\DiInterface $di
     */
    public function __construct(DiInterface $di, $arguments)
    {
        $this->_di = $di;
    }

    /**
     * Get acl system
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function _()
    {
        if (!$this->_acl) {

            $cacheData = $this->_di->get('cacheData');

            $acl = $cacheData->get(self::ACL_CACHE_KEY);
            if ($acl === null) {

                $acl = new AclMemory();
                $acl->setDefaultAction(PhAcl::DENY);

                // prepare Roles
                $roles = \User\Model\Role::find();
                $roleNames = array();
                foreach ($roles as $role) {
                    $roleNames[$role->id] = $role->name;
                    $acl->addRole($role->name);
                }

                // Defining admin area
                $adminArea = new AclResource(self::ACL_ADMIN_AREA);
                $roleAdmin = \User\Model\Role::getRoleByType(self::DEFAULT_ROLE_ADMIN);
                // Add "admin area" resource
                $acl->addResource($adminArea, "access");
                $acl->allow($roleAdmin->name, self::ACL_ADMIN_AREA, 'access');


                // Getting objects that is in acl
                // Looking for all models in modelsDir and check @Acl annotation
                $objects = array(
                    self::ACL_ADMIN_AREA => array(
                        'actions' => array('access')
                    )
                );
                $config = $this->_di->get('config');
                foreach ($this->_di->get('modules') as $module => $enabled) {

                    if (!$enabled) {
                        continue;
                    }

                    $moduleName = ucfirst($module);

                    $modelsPath = $config->application->modulesDir . $moduleName . '/Model';
                    if (file_exists($modelsPath)) {

                        $files = scandir($modelsPath); // get all file names

                        foreach ($files as $file) { // iterate files
                            if ($file == "." || $file == "..") {
                                continue;
                            }
                            $class = sprintf('\%s\Model\%s', $moduleName, ucfirst(str_replace('.php', '', $file)));
                            $object = $this->getObjectAcl($class);
                            if ($object == null) continue;

                            $objects[$class]['actions'] = $object->actions;
                            $objects[$class]['options'] = $object->options;
                        }

                        // add objects to resources
                        foreach ($objects as $key => $object) {
                            if (empty($object['actions'])) {
                                $object['actions'] = array();
                            }
                            $acl->addResource($key, $object['actions']);
                        }
                    }
                }

                // load from database
                $access = \Core\Model\Access::find();

                foreach ($access as $item) {

                    $value = $item->value;

                    if (array_key_exists($item->object, $objects) && in_array($item->action, $objects[$item->object]['actions']) && ($value == "allow" || $value == "deny")) {
                        $acl->$value($roleNames[$item->role_id], $item->object, $item->action);
                    }
                }

                $cacheData->save(self::ACL_CACHE_KEY, $acl, 2592000); // 30 days cache
            }

            $this->_acl = $acl;
        }
        return $this->_acl;
    }

    public function getAllowedValue($objectName, \User\Model\Role $role, $option)
    {
        $result = \Core\Model\Access::findFirst(array(
            "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
            "bind" => array(
                1 => $objectName,
                2 => $option,
                3 => $role->id
            )
        ));

        if ($result) {
            return $result->value;
        }

        return null;
    }

    public function getObjectAcl($objectName)
    {
        $object = new \stdClass();
        $object->name = $objectName;
        $object->actions = array();
        $object->options = array();

        if ($objectName == self::ACL_ADMIN_AREA) {
            $object->actions = array('access');

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
     * Clear acl cache. The acl will be rewrited.
     */
    public function clearAcl()
    {
        $this->_di->get('cacheData')->delete(self::ACL_CACHE_KEY);
    }

    /**
     * This action is executed before execute any action in the application
     */
    public function beforeDispatch(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $viewer = \User\Model\User::getViewer();
        $acl = $this->_();

        $controller = $dispatcher->getControllerName();

        // check admin area
        if (substr($controller, 0, 5) == 'Admin') {

            if ($acl->isAllowed($viewer->getRole()->name, self::ACL_ADMIN_AREA, 'access') != \Phalcon\Acl::ALLOW) {
                return $dispatcher->forward(array(
                    'module' => \Engine\Application::$defaultModule,
                    'namespace' => ucfirst(\Engine\Application::$defaultModule) . '\Controller',
                    "controller" => 'error',
                    "action" => 'show404'
                ));
            }
        }

    }
}