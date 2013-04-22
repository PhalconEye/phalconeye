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
 * @RoutePrefix("/admin")
 */
class AdminIndexController extends \Core\Controller\BaseAdmin
{
    /**
     * @Get("/", name="admin-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->debug = $this->config->application->debug;
    }

    /**
     * @Get("/mode", name="admin-mode")
     */
    public function modeAction()
    {
        $this->view->disable();

        $this->config->application->debug = (bool)$this->request->get('debug', null, true);
        $configText = var_export($this->config->toArray(), true);
        $configText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $configText);
        file_put_contents(ROOT_PATH . '/app/config/config.php', "<?php " . PHP_EOL . PHP_EOL . "return new \\Phalcon\\Config(" . $configText . ");");

        // clear cache
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

    }
}

