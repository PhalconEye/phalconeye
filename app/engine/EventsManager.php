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

namespace Engine;

class EventsManager extends \Phalcon\Events\Manager{

    public function __construct(){
        $config = \Phalcon\DI::getDefault()->get('config');

        // Attach modules plugins events
        $modules = $config->get('events')->toArray();
        $loadedModules = $config->modules->toArray();
        if (!empty($modules)){
            foreach($modules as $module => $events){
                if (!in_array($module, $loadedModules)) continue;

                foreach($events as $event){
                    $pluginClass = $event['namespace'] . '\\' . $event['class'];
                    $this->attach($event['type'], new $pluginClass());
                }
            }
        }

        // Attach plugins events
        $plugins = $config->get('plugins');
        if (!empty($plugins)){
            foreach($plugins as $pluginName => $plugin){
                if (!$plugin['enabled'] || empty($plugin['events']) || !is_array($plugin['events'])) continue;
                $pluginClass = '\Plugin\\'.ucfirst($pluginName) . '\\' .ucfirst($pluginName);
                foreach($plugin['events'] as $event){
                    $this->attach($event, new $pluginClass());
                }
            }
        }
    }
}