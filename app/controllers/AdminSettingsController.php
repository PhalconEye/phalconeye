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

class AdminSettingsController extends Controller
{

    public function indexAction()
    {
        $form = new Form_Admin_Settings_System();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $data = $form->getData();
        Settings::setSettings($data);
    }

    public function performanceAction()
    {
        $form = new Form_Admin_Settings_Performance();
        $this->view->setVar('form', $form);

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

        $form->setData($cacheData);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $data = $form->getData();
        if (!empty($data['clear_cache']) && $data['clear_cache'] = 1) {
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

            $form->addNotice('Cache cleared!');
            $form->setElementParam('clear_cache', 'value', null);
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
        $configText = var_export($this->config->toArray(), true);
        $configText = str_replace("'".ROOT_PATH, "ROOT_PATH . '", $configText);
        file_put_contents(ROOT_PATH . '/app/config/config.php', "<?php " . PHP_EOL . PHP_EOL . "return new \\Phalcon\\Config(" . $configText . ");");
    }
}

