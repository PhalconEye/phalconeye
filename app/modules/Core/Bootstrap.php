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

namespace Core;

use Phalcon\DiInterface,
    Phalcon\Translate\Adapter\NativeArray as TranslateArray;

class Bootstrap extends \Engine\Bootstrap
{
    protected $_moduleName = "Core";

    public static function dependencyInjection(DiInterface $di)
    {
        self::_initWidgets($di);
        self::_initLocale($di);
    }

    /**
     * Prepare widgets metadata for Engine
     */
    private static function _initWidgets($di)
    {
        $cache = $di->get('cacheData');
        $cacheKey = "widgets_metadata.cache";
        $widgets = $cache->get($cacheKey);

        if ($widgets === null){
            $widgetObjects = \Core\Model\Widget::find();
            $widgets = array();
            foreach($widgetObjects as $object){
                $widgets[$object->getId()] = $object;
            }

            $cache->save($cacheKey, $widgets, 2592000); // 30 days
        }
        \Engine\Widget\Storage::setWidgets($widgets);
    }

    /**
     * Init locale
     *
     * @param $di
     */
    private static function _initLocale($di)
    {
        $locale = $di->get('session')->get('locale', \Core\Model\Settings::getSetting('system_default_language'));
        $translate = null;

        if (!$di->get('config')->application->debug) {
            $messages = array();
            if (file_exists(ROOT_PATH . "/app/var/languages/" . $locale . ".php")) {
                require ROOT_PATH . "/app/var/languages/" . $locale . ".php";
            } else {
                if (file_exists(ROOT_PATH . "/app/var/languages/en.php")) {
                    // fallback to default
                    require ROOT_PATH . "/app/var/languages/en.php";
                }
            }

            $translate = new TranslateArray(array(
                "content" => $messages
            ));
        } else {
            $translate = new \Engine\Translation\Db(array(
                'db' => $di->get('db'),
                'locale' => $locale,
                'model' => 'Core\Model\Language',
                'translationModel' => 'Core\Model\LanguageTranslation'
            ));
        }

        $di->set('trans', $translate);
    }
}