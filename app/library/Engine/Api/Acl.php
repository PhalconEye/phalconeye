<?php

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
            $aclData = $this->cacheData->get(self::ACL_CACHE_KEY);
            $acl = null;
            if ($aclData === null){
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


                $this->cacheData->save(self::ACL_CACHE_KEY, $acl, 10000000);
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


    }
}