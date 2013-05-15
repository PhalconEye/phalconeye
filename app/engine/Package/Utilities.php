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

    static public function fsCheckLocation($path){
        if (!is_dir($path)){
            @mkdir(dirname($path), 0755, true);
        }
    }

    static public function fsCopyRecursive($source, $dest)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::KEY_AS_PATHNAME), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $item) {
            $partial = str_replace($source, '', $item->getPathname());
            $fDest = rtrim($dest, '/\\') . $partial;
            // Ignore errors on mkdir (only fail if the file fails to copy
            if ($item->isDir()) {
                @mkdir($fDest, $item->getPerms(), true);
            } else if ($item->isFile()) {
                @mkdir(dirname($fDest), 0755, true);
                if (!copy($item->getPathname(), $fDest)) {
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