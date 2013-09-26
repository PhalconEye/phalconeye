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

namespace Core\Controller;

/**
 * @RoutePrefix("/admin/module", name="admin-module")
 */
class AdminModuleController extends \Core\Controller\BaseAdmin
{
    /**
     * @Route("/{name:[a-zA-Z0-9_-]+}", methods={"GET"}, name="admin-module-index")
     */
    public function indexAction($name)
    {
        $className = ucfirst($name) . '\Controller\AdminIndexController';
        if (class_exists($className)){
            return $this->dispatcher->forward(array(
                'module' => $name,
                "controller" => "adminindex",
                "action" => "index"
            ));
        }
    }

}

