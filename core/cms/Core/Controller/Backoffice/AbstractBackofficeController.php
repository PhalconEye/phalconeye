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

use Core\Controller\AbstractController;
use Core\Model\SettingsModel;
use Core\Navigation\Backoffice\MainNavigation;
use Engine\Asset\Manager as AssetManager;

/**
 * Base admin controller.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractBackofficeController extends AbstractController
{
    /**
     * Initialize admin specific logic.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->setIsBackoffice(true);

        if ($this->request->isAjax()) {
            return;
        }

        $this->view->adminNavigation = new MainNavigation();
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
                ->addCss('libs/bootstrap/css/bootstrap.css')
                ->addCss('libs/bootstrap/css/bootstrap-switch.css')
                ->addCss('libs/jquery/jquery-ui.css')
                ->addCss('application/css/core/backoffice/main.css')
        );

        $this->assets->get(AssetManager::DEFAULT_COLLECTION_JS)
            ->addJs('libs/bootstrap/js/bootstrap.js')
            ->addJs('libs/bootstrap/js/bootstrap-switch.js')
            ->addJs('libs/ckeditor/ckeditor.js');
    }

    /**
     * Clear cache
     *
     * @return void
     */
    protected function _clearCache()
    {
        $this->app->clearCache();
    }
}

