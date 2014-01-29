<?php

/**
 * This file is part of the Symfony2-coding-standard (phpcs standard)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Symfony2-phpcs-authors <Symfony2-coding-standard@opensky.github.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @version  GIT: master
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */

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

/**
 * PhalconEye_Sniffs_Classes_MultipleClassesOneFileSniff.
 *
 * Throws errors if multiple classes are defined in a single file.
 *
 * Symfony coding standard specifies: "Define one class per file;"
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */
class PhalconEye_Sniffs_Classes_MultipleClassesOneFileSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * The number of times the T_CLASS token is encountered in the file.
     *
     * @var int
     */
    protected $classCount = 0;

    /**
     * The current file this class is operating on.
     *
     * @var string
     */
    protected $currentFile;

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->currentFile !== $phpcsFile->getFilename()) {
            $this->classCount  = 0;
            $this->currentFile = $phpcsFile->getFilename();
        }

        $this->classCount++;

        if ($this->classCount > 1) {
            $phpcsFile->addError(
                'Multiple classes defined in a single file',
                $stackPtr
            );
        }

        return;
    }
}
