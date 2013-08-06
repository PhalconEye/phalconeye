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

namespace Engine\Package;

class Utilities
{
    // Fs helpers

    static public function fsCheckLocation($path)
    {
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    static public function fsCopyRecursive($source, $dest, $statFiles = false, $excludeNames = array())
    {
        if (!is_dir($source)) {
            return;
        }

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::KEY_AS_PATHNAME), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $item) {
            $itemPath = $item->getPathname();
            $partial = str_replace($source, '', $itemPath);
            if (in_array($partial, $excludeNames)) {
                continue;
            }
            if ($partial == '.' || $partial == '..') continue;

            $fDest = rtrim($dest, '/\\') . $partial;
            // Ignore errors on mkdir (only fail if the file fails to copy
            if ($item->isDir()) {
                if (!is_dir($fDest))
                    @mkdir($fDest, $item->getPerms(), 0755, true);
            } else if ($item->isFile()) {
                if ($statFiles && (is_file($fDest) && filemtime($itemPath) <= filemtime($fDest))) {
                    continue;
                }
                
                if (!copy($itemPath, $fDest)) {
                    throw new Exception('Unable to copy.');
                }
            }
        }
    }

    static public function fsRmdirRecursive($path, $includeSelf = false)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $key => $child) {
            if ($child->getFilename() == '.' || $child->getFilename() == '..') {
                continue;
            }
            if ($it->isDir()) {
                if (!rmdir($key)) {
                    throw new \Exception(sprintf('Unable to remove directory: %s', $key));
                }
            } else if ($it->isFile()) {
                if (!unlink($key)) {
                    throw new Exception(sprintf('Unable to remove file: %s', $key));
                }
            }
        }

        if (is_dir($path) && $includeSelf) {
            if (!rmdir($path)) {
                throw new Exception(sprintf('Unable to remove directory: %s', $path));
            }
        }
    }

    static public function fsRecursiveGlob($defaultPath = '', $pattern = '*', $flags = 0)
    {
        $paths = glob($defaultPath . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($defaultPath . $pattern, $flags);
        foreach ($paths as $path) {
            $files = array_merge($files, self::fsRecursiveGlob($path, $pattern, $flags));
        }
        return $files;
    }
}