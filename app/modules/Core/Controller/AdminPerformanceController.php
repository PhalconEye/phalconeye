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
 * @RoutePrefix("/admin/performance", name="admin-performance")
 */
class AdminPerformanceController extends \Core\Controller\BaseAdmin
{
    /**
     * @Route("/", methods={"GET", "POST"}, name="admin-performance")
     */
    public function indexAction()
    {
        $form = new \Core\Form\Admin\Setting\Performance();
        $this->view->form = $form;

        $cacheData = $this->config->application->cache->toArray();

        switch ($this->config->application->cache->adapter) {
            case "File":
            {
                $cacheData['adapter'] = 0;

            }
                break;
            case "Memcache":
            {
                $cacheData['adapter'] = 1;
            }
                break;
            case "Apc":
            {
                $cacheData['adapter'] = 2;
            }
                break;
            case "Mongo":
            {
                $cacheData['adapter'] = 3;
            }
                break;
        }

        $form->setValues($cacheData);

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $data = $form->getValues();
        if (!empty($data['clear_cache']) && $data['clear_cache'] = 1) {
            $keys = $this->viewCache->queryKeys();
            foreach ($keys as $key) {
                $this->viewCache->delete($key);
            }

            $keys = $this->cacheOutput->queryKeys();
            foreach ($keys as $key) {
                $this->cacheOutput->delete($key);
            }

            $keys = $this->cacheData->queryKeys();
            foreach ($keys as $key) {
                $this->cacheData->delete($key);
            }

            $keys = $this->modelsCache->queryKeys();
            foreach ($keys as $key) {
                $this->modelsCache->delete($key);
            }

            // clear files cache
            $files = glob($this->config->application->cache->cacheDir . '*'); // get all file names
            foreach($files as $file){ // iterate files
                if(is_file($file))
                    @unlink($file); // delete file
            }

            // clear view cache
            $files = glob($this->config->application->view->compiledPath . '*'); // get all file names
            foreach($files as $file){ // iterate files
                if(is_file($file))
                    @unlink($file); // delete file
            }

            $this->flash->success('Cache cleared!');
            $form->setValue('clear_cache', null);
        }


        $cacheData = array(
            'lifetime' => $data['lifetime'],
            'prefix' => $data['prefix']
        );

        switch ($data['adapter']) {
            case 0:
            {
                $cacheData['adapter'] = 'File';
                $cacheData['cacheDir'] = $data['cacheDir'];
            }
                break;
            case 1:
            {
                $cacheData['adapter'] = 'Memcache';
                $cacheData['host'] = $data['host'];
                $cacheData['port'] = $data['port'];
                $cacheData['persistent'] = $data['persistent'];
            }
                break;
            case 2:
            {
                $cacheData['adapter'] = 'Apc';
            }
                break;
            case 3:
            {
                $cacheData['adapter'] = 'Mongo';
                $cacheData['server'] = $data['server'];
                $cacheData['db'] = $data['db'];
                $cacheData['collection'] = $data['collection'];
            }
                break;
        }

        $this->config->application->cache = new \Phalcon\Config($cacheData);
        $this->app->saveConfig();
        $this->flash->success('Settings saved!');
    }
}

