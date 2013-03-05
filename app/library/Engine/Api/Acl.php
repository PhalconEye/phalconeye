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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Api_Acl extends \Phalcon\Mvc\User\Plugin{

    const ACL_CACHE_KEY = "acl_data.cache";
    const ROLE_TYPE_ADMIN = 'admin';
    const ROLE_TYPE_USER = 'user';
    const ROLE_TYPE_GUEST = 'guest';

    const ACL_ADMIN_AREA = 'AdminArea';

    /**
     * @var Phalcon\Acl\Adapter\Memory
     */
    protected $_acl;

    /**
     * @param \Phalcon\DiInterface $di
     */
    public function __construct($di)
    {
        $this->_dependencyInjector = $di;
    }

    /*
     * Short cast of getAcl()
     */
    public function _(){
        return $this->getAcl();
    }

    /**
     * Get acl system
     *
     * @return Memory|Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        if (!$this->_acl)
        {
            $acl = $this->cacheData->get(self::ACL_CACHE_KEY);
            if ($acl === null){
                $acl = new \Phalcon\Acl\Adapter\Memory();
                $acl->setDefaultAction(\Phalcon\Acl::DENY);

                // Adding adming as role
                $roleAdminObject = Role::getRoleByType(self::ROLE_TYPE_ADMIN);
                $acl->addRole($roleAdminObject->getName());

                // Defining admin area
                $adminArea = new \Phalcon\Acl\Resource(self::ACL_ADMIN_AREA);
                // Add "admin area" resource
                $acl->addResource($adminArea, "access");
                $acl->allow($roleAdminObject->getName(), self::ACL_ADMIN_AREA, 'access');


                $this->cacheData->save(self::ACL_CACHE_KEY, $acl, 2592000); // 30 days cache
            }



            $this->_acl = $acl;
        }
        return $this->_acl;
    }

    /**
     * Clear acl cache. The system will rework acl from database
     */
    public function clearAcl(){
        $this->cacheData->delete(self::ACL_CACHE_KEY);
    }

    /**
     * This action is executed before execute any action in the application
     */
    public function beforeDispatch(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $viewer = User::getViewer();
        $acl = $this->_();

        $controller = $dispatcher->getControllerName();

        // check admin area
        if (substr($controller,0, 5) == 'admin'){
            if ($acl->isAllowed($viewer->getRole()->getName(), Api_Acl::ACL_ADMIN_AREA, 'access') != \Phalcon\Acl::ALLOW){
                return  $this->dispatcher->forward(array(
                    "controller" => 'error',
                    "action" => 'show404'
                ));
            }
        }

    }
}