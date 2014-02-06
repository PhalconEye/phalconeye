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

use Engine\Api\AbstractApi;
use Phalcon\DiInterface;

/**
 * Auth api.
 *
 * @category  PhalconEye
 * @package   Core\Api
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Auth extends AbstractApi
{
    private $_identity = 0;

    /**
     * Create api.
     *
     * @param DiInterface $di        Dependency injection.
     * @param array       $arguments Api arguments.
     */
    public function __construct(DiInterface $di, $arguments)
    {
        parent::__construct($di, $arguments);
        $this->_identity = $this->getDI()->get('session')->get('identity', 0);
    }

    /**
     * Authenticate user.
     *
     * @param int $identity User identity.
     *
     * @return void
     */
    public function authenticate($identity)
    {
        $this->_identity = $identity;
        $this->getDI()->get('session')->set('identity', $identity);
    }

    /**
     * Clear identity, logout.
     *
     * @return void
     */
    public function clearAuth()
    {
        $this->_identity = 0;
        $this->getDI()->get('session')->set('identity', 0);
    }

    /**
     * Get current identity.
     *
     * @return int
     */
    public function getIdentity()
    {
        return $this->_identity;
    }
}