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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller;

use Core\Model\Settings;
use Core\Navigation\AdminNavigation;
use Engine\Asset\Manager as AssetManager;

/**
 * Base admin controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractAdminController extends AbstractController
{
    /**
     * Initialize admin specific logic.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->request->isAjax()) {
            return;
        }

        $this->view->adminNavigation = new AdminNavigation();

        $this->_setupAssets();
    }

    /**
     * Setup assets files.
     *
     * @return void
     */
    protected function _setupAssets()
    {
        parent::_setupAssets();

        // Assets setup.
        $this->assets->set(
            AssetManager::DEFAULT_COLLECTION_CSS,
            $this->assets->getEmptyCssCollection()
                ->addCss('external/bootstrap/css/bootstrap.min.css')
                ->addCss('external/bootstrap/css/bootstrap-switch.min.css')
                ->addCss('external/jquery/jquery-ui.css')
                ->addCss('assets/css/core/admin/main.css')
                ->join(false)
        );

        $this->assets->get(AssetManager::DEFAULT_COLLECTION_JS)
            ->addJs('external/bootstrap/js/bootstrap.min.js')
            ->addJs('external/bootstrap/js/bootstrap-switch.min.js')
            ->addJs('external/ckeditor/ckeditor.js');
    }

    /**
     * Clear cache
     *
     * @return void
     */
    protected function _clearCache()
    {
        $this->app->clearCache(PUBLIC_PATH . '/themes/' . Settings::getValue('system', 'theme'));
    }
}

