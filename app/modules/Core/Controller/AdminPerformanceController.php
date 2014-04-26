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

namespace Core\Controller;

use Core\Form\Admin\Setting\Performance as PerformanceForm;
use Core\Model\Settings;
use Phalcon\Config;

/**
 * Admin performance settings.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/performance", name="admin-performance")
 */
class AdminPerformanceController extends AbstractAdminController
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET", "POST"}, name="admin-performance")
     */
    public function indexAction()
    {
        $form = new PerformanceForm();
        $this->view->form = $form;

        $cacheData = $this->config->application->cache->toArray();

        switch ($this->config->application->cache->adapter) {
            case "File":
                $cacheData['adapter'] = 0;
                break;
            case "Memcache":
                $cacheData['adapter'] = 1;
                break;
            case "Apc":
                $cacheData['adapter'] = 2;
                break;
            case "Mongo":
                $cacheData['adapter'] = 3;
                break;
        }

        $form->setValues($cacheData);

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $data = $form->getValues();
        if (!empty($data['clear_cache']) && $data['clear_cache'] = 1) {
            $this->app->clearCache(PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme'));
            $this->flash->success('Cache cleared!');
            $form->setValue('clear_cache', null);
        }

        $cacheData = ['lifetime' => $data['lifetime'], 'prefix' => $data['prefix']];

        switch ($data['adapter']) {
            case 0:
                $cacheData['adapter'] = 'File';
                $cacheData['cacheDir'] = $data['cacheDir'];
                break;
            case 1:
                $cacheData['adapter'] = 'Memcache';
                $cacheData['host'] = $data['host'];
                $cacheData['port'] = $data['port'];
                $cacheData['persistent'] = $data['persistent'];
                break;
            case 2:
                $cacheData['adapter'] = 'Apc';
                break;
            case 3:
                $cacheData['adapter'] = 'Mongo';
                $cacheData['server'] = $data['server'];
                $cacheData['db'] = $data['db'];
                $cacheData['collection'] = $data['collection'];
                break;
        }

        $this->config->application->cache = new Config($cacheData);
        $this->config->save();
        $this->flash->success('Settings saved!');
    }
}

