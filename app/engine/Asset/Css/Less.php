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

namespace Engine\Asset\Css;

/**
 * Less layer.
 *
 * @category  PhalconEye
 * @package   Engine\Asset\Css
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Less
{
    /**
     * Less compiler.
     *
     * @var \lessc
     */
    protected $_lessc;

    /**
     * Files to compile.
     *
     * @var array
     */
    protected $_files = [];

    /**
     * Import directories.
     *
     * @var array
     */
    protected $_importDirs = [];

    /**
     * Create less layer.
     */
    public function __construct()
    {
        $this->_lessc = self::factory();
    }

    /**
     * Returns Less compiler.
     *
     * @return \lessc
     */
    public static function factory()
    {
        require_once "lessc.inc.php";

        return new \lessc;
    }

    /**
     * Get compiler object.
     *
     * @return \lessc
     */
    public function getCompiler()
    {
        return $this->_lessc;
    }

    /**
     * Add file for compilation.
     *
     * @param string $file File path.
     *
     * @return $this
     */
    public function addFile($file)
    {
        $this->_files[] = $file;

        return $this;
    }

    /**
     * Add directory for compilation.
     *
     * @param string $directory Directory path.
     *
     * @return $this
     */
    public function addDir($directory)
    {
        $directory = rtrim($directory, '/');
        $files = glob($directory . '/*.less');
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Add import directory for compilation.
     *
     * @param string $directory Directory path.
     *
     * @return $this
     */
    public function addImportDir($directory)
    {
        $this->_importDirs[] = $directory;

        return $this;
    }

    /**
     * Compile all files into one file.
     *
     * @param string $filePath File location.
     * @param string $format   Formatter for less.
     *
     * @return void
     */
    public function compileTo($filePath, $format = 'compressed')
    {
        $less = '';
        foreach ($this->_files as $file) {
            $less .= file_get_contents($file) . "\n\n";
        }

        $this->_lessc->setImportDir($this->_importDirs);
        $this->_lessc->setFormatter($format);
        $result = $this->_lessc->compile($less);

        file_put_contents($filePath, $result);
    }
}