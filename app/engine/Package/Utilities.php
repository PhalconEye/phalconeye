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

namespace Engine\Package;

use Engine\Exception;

/**
 * Some utilities.
 *
 * @category  PhalconEye
 * @package   Engine\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Utilities
{
    /**
     * Create path if it doesn't exists.
     *
     * @param string $path Path.
     */
    static public function fsCheckLocation($path)
    {
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }

    /**
     * Copy files and directories recursively.
     *
     * @param string $source       Copy from.
     * @param string $dest         Copy to path.
     * @param bool   $statFiles    Check files with stat command.
     * @param array  $excludeNames Exclude names from coping.
     *
     * @throws \Engine\Exception
     */
    static public function fsCopyRecursive($source, $dest, $statFiles = false, $excludeNames = [])
    {
        if (!is_dir($source)) {
            return;
        }

        $iteratorFlags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, $iteratorFlags),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $itemPath => $item) {
            $partial = str_replace($source, '', $itemPath);
            if (in_array($partial, $excludeNames) || in_array(basename($itemPath), $excludeNames)) {
                continue;
            }

            $fDest = rtrim($dest, '/\\') . DS . $partial;
            // Ignore errors on mkdir (only fail if the file fails to copy
            if ($item->isDir() && !is_dir($fDest)) {
                @mkdir($fDest, $item->getPerms(), true);
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

    /**
     * Remove directory recursively.
     *
     * @param string $path        Path to remove.
     * @param bool   $includeSelf Including root dir.
     *
     * @throws Exception
     */
    static public function fsRmdirRecursive($path, $includeSelf = false)
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $key => $child) {
            if ($child->getFilename() == '.' || $child->getFilename() == '..') {
                continue;
            }
            if ($it->isDir()) {
                if (!rmdir($key)) {
                    throw new Exception(sprintf('Unable to remove directory: %s', $key));
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

    /**
     * List files recursively.
     *
     * @param string $defaultPath Path to look for files.
     * @param string $pattern     Search pattern.
     * @param int    $flags       Some glob flags.
     *
     * @return array
     */
    static public function fsRecursiveGlob($defaultPath = '', $pattern = '*', $flags = 0)
    {
        $paths = glob($defaultPath . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($defaultPath . $pattern, $flags);
        if ($paths === false) {
            if ($files === false) {
                return array();
            }
            return $files; // error or empty match for sub directories.
        }
        foreach ($paths as $path) {
            $files = array_merge($files, self::fsRecursiveGlob($path, $pattern, $flags));
        }

        return $files;
    }

    /**
     * Get relative path.
     *
     * @param string $from From path.
     * @param string $to   To path.
     *
     * @return string
     */
    static public function getRelativePath($from, $to)
    {
        // Some compatibility fixes for Windows paths.
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // Find first non-matching dir.
            if ($dir === $to[$depth]) {
                // Ignore this directory.
                array_shift($relPath);
            } else {
                // Get number of remaining dirs to $from.
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // Add traversals up to first matching dir.
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }
}