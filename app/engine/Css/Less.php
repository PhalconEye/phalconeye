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

namespace Engine\Css;

class Less
{
    protected $_lessc;
    protected $_files = array();
    protected $_importDirs = array();

    public function __construct()
    {
        $this->_lessc = self::factory();
    }

    /**
     * Returns Less compiler
     * @return \lessc
     */
    public static function factory()
    {
        require_once "lessc.inc.php";
        return new \lessc;
    }

    public function getCompiler(){
        return $this->_lessc;
    }

    /**
     * Add file for compilation
     *
     * @param $file
     * @return $this
     */
    public function addFile($file)
    {
        $this->_files[] = $file;
        return $this;
    }

    /**
     * Add directory for compilation
     *
     * @param $directory
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
     * Add import directory for compilation
     *
     * @param $directory
     * @return $this
     */
    public function addImportDir($directory) {
        $this->_importDirs[] = $directory;
        return $this;
    }

    /**
     * Compile all files into one file
     *
     * @param $filepath - out put file
     */
    public function compileTo($filepath, $format = 'compressed'){
        $less = '';
        foreach ($this->_files as $file) {
            $less .= file_get_contents($file) . "\n\n";
        }

        try {
            $this->_lessc->setImportDir($this->_importDirs);
            $this->_lessc->setFormatter($format);
            $result = $this->_lessc->compile($less);

            file_put_contents($filepath, $result);
        }
        catch (\Engine\Exception $e) {
            error_log('Exception: ' . $e->getMessage());
        }
    }

}