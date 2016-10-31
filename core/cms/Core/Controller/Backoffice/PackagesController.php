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

namespace Core\Controller\Backoffice;

use Core\Navigation\Backoffice\PackagesNavigation;


/**
 * Admin packages controller.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/packages", name="backoffice-packages")
 */
class PackagesController extends AbstractBackofficeController
{
    /**
     * Init controller's navigation.
     *
     * @return void
     */
    public function init()
    {
        $this->view->navigation = new PackagesNavigation();
    }

    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="backoffice-packages")
     */
    public function indexAction()
    {
        $this->view->packages = $this->getDI()->getModules()->getPackages();
    }

    /**
     * Widgets action.
     *
     * @return void
     *
     * @Route("/widgets", methods={"GET"}, name="backoffice-packages-widgets")
     */
    public function widgetsAction()
    {
        $this->view->packages = $this->getDI()->getWidgets()->getPackages();
        $this->view->pick('Packages/index', 'core', true);
    }

    /**
     * Plugins action.
     *
     * @Route("/plugins", methods={"GET"}, name="backoffice-packages-plugins")
     */
    public function pluginsAction()
    {
        $this->view->packages = $this->getDI()->getPlugins()->getPackages();
        $this->view->pick('Packages/index', 'core', true);
    }

    /**
     * Enable package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/enable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET"},
     * name="backoffice-packages-enable"
     * )
     */
    public function enableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            $package->enabled = 1;
            $package->save();

            $this->_enablePackageConfig($package);
            $this->_updateMetadata();
            $this->_clearCache();
        }

        return $this->response->redirect(['for' => $return]);
    }

    /**
     * Disable package.
     *
     * @param string $type   Package type.
     * @param string $name   Package name.
     * @param string $return Return to.
     *
     * @return mixed
     *
     * @Route(
     * "/disable/{type:[a-zA-Z0-9_-]+}/{name:[a-zA-Z0-9_-]+}/{return:[a-zA-Z0-9_-]+}",
     * methods={"GET"},
     * name="backoffice-packages-disable"
     * )
     */
    public function disableAction($type, $name, $return)
    {
        $this->view->disable();

        $package = $this->_getPackage($type, $name);
        if ($package && !$package->is_system) {
            if ($this->_hasDependencies($package)) {
                return $this->response->redirect(['for' => $return]);
            }

            $package->enabled = 0;
            $package->save();

            $this->_disablePackageConfig($package);
            $this->_updateMetadata();
            $this->_clearCache();
        }

        return $this->response->redirect(['for' => $return]);
    }
}