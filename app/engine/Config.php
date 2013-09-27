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

class Config
{
    /**
     * Save application config to file
     *
     * @param \Phalcon\Config $config
     */
    public static function save($config = null)
    {
        $configText = var_export($config->toArray(), true);

        // Fix pathes. This related to windows directory separator.
        $configText = str_replace('\\\\', DS, $configText);

        $configText = str_replace("'" . PUBLIC_PATH, "PUBLIC_PATH . '", $configText);
        $configText = str_replace("'" . ROOT_PATH, "ROOT_PATH . '", $configText);
        $configText = '<?php

/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

/**
* WARNING
*
* Manual changes to this file may cause a malfunction of the system.
* Be careful when changing settings!
*
*/

return new \\Phalcon\\Config(' . $configText . ');';
        file_put_contents(ROOT_PATH . Application::SYSTEM_CONFIG_PATH, $configText);
    }
}