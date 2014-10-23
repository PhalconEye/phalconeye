<?php
/**
 * Squiz_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
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

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Squiz_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of variables and member variables.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.5
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PhalconEye_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    private $_magicMethods = array(
        'construct',
        'destruct',
        'call',
        'callStatic',
        'get',
        'set',
        'isset',
        'unset',
        'sleep',
        'invoke',
        'wakeup',
        'toString',
        'set_state',
        'clone'
    );

    private $_escapeMethods = array(
        'beforeSave',
        'afterSave',
        'beforeValidation',
        'beforeValidationOnCreate',
        'beforeValidationOnUpdate',
        'onValidationFails',
        'afterValidationOnCreate',
        'afterValidationOnUpdate',
        'afterValidation',
        'beforeUpdate',
        'beforeCreate',
        'beforeDelete',
        'afterFetch',
        'afterUpdate',
        'afterCreate',
        '_', // reserved, maybe for translation
        '__', // reserved, maybe for translation
    );

    /**
     * A list of all PHP magic functions.
     *
     * @var array
     */
    private $_magicFunctions = array(
        'autoload',
    );

    /**
     * Constructs a PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);

    }//end __construct()

    /**
     * Processes the tokens within the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                  $stackPtr  The position where this token was
     *                                        found.
     * @param int                  $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $className = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        // Check some cases to escape.
        if (in_array($methodName, $this->_escapeMethods)) {
            return;
        }

        // Dont' check traits. Coz it's not correct.
        $traitCheck = $phpcsFile->findPrevious(array(T_TRAIT), $stackPtr - 1);
        if ($traitCheck) {
            return;
        }

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = substr($methodName, 2);
            if (in_array($magicPart, $this->_magicMethods) === false) {
                $error = "Method name \"$className::$methodName\" is invalid; only PHP magic methods should be prefixed with a double underscore";
                $phpcsFile->addError($error, $stackPtr);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodName === '_' . $className) {
            return;
        }

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        $isPublic = (in_array($methodProps['scope'], array('private', 'protected'))) ? false : true;
        $scope = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

        // If it's a private method, it must have an underscore on the front.
        if ($isPublic === false && $methodName{0} !== '_') {
            $error = "Private method name \"$className::$methodName\" must be prefixed with an underscore";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // If it's not a private method, it must not have an underscore on the front.
        if ($isPublic === true && $scopeSpecified === true && $methodName{0} === '_') {
            $error = ucfirst($scope) . " method name \"$className::$methodName\" must not be prefixed with an underscore";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // If the scope was specified on the method, then the method must be
        // camel caps and an underscore should be checked for. If it wasn't
        // specified, treat it like a public method and remove the underscore
        // prefix if there is one because we cant determine if it is private or
        // public.
        $testMethodName = $methodName;
        if ($scopeSpecified === false && $methodName{0} === '_') {
            $testMethodName = substr($methodName, 1);
        }

        // If it's not a lambda function and doesn't
        if (!$this->isLambda($methodName) && PHP_CodeSniffer::isCamelCaps($testMethodName, false, $isPublic, false) === false) {
            if ($scopeSpecified === true) {
                $error = ucfirst($scope) . " method name \"$className::$methodName\" is not in camel caps format";
            } else {
                $error = "Method name \"$className::$methodName\" is not in camel caps format";
            }

            $phpcsFile->addError($error, $stackPtr);
            return;
        }

    }//end processTokenWithinScope()

    /**
     *
     * Test to see whether the function name exists, if not, it's a lambda
     * function
     *
     * @param string $functionName
     *
     * @todo Beef this up a bit by testing to see whether or not it's a lambda
     *       function, or someone just wrong "function (){ }"
     */
    private function isLambda($functionName)
    {
        // $functionName = function($args) { /** do Stuff **/ }
        if (trim($functionName) === '') {
            return true;
        }

        return false;
    }

    /**
     * Processes the tokens outside the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                  $stackPtr  The position where this token was
     *                                        found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        // Check some cases to escape.
        if (in_array($functionName, $this->_escapeMethods)) {
            return;
        }

        // Dont' check traits. Coz it's not correct.
        $traitCheck = $phpcsFile->findPrevious(array(T_TRAIT), $stackPtr - 1);
        if ($traitCheck) {
            return;
        }


        // Is this a magic function. IE. is prefixed with "__".
        if (preg_match('|^__|', $functionName) !== 0) {
            $magicPart = substr($functionName, 2);
            if (in_array($magicPart, $this->_magicFunctions) === false) {
                $error = "Function name \"$functionName\" is invalid; only PHP magic methods should be prefixed with a double underscore";
                $phpcsFile->addError($error, $stackPtr);
            }

            return;
        }

        // Function names can be in two parts; the package name and
        // the function name.
        $packagePart = '';
        $camelCapsPart = '';
        $underscorePos = strrpos($functionName, '_');
        if ($underscorePos === false) {
            $camelCapsPart = $functionName;
        } else {
            $packagePart = substr($functionName, 0, $underscorePos);
            $camelCapsPart = substr($functionName, ($underscorePos + 1));

            // We don't care about _'s on the front.
            $packagePart = ltrim($packagePart, '_');
        }

        // If it has a package part, make sure the first letter is a capital.
        if ($packagePart !== '') {
            if ($functionName{0} === '_') {
                $error = "Function name \"$functionName\" is invalid; only private methods should be prefixed with an underscore";
                $phpcsFile->addError($error, $stackPtr);
                return;
            }

            if ($functionName{0} !== strtoupper($functionName{0})) {
                $error = "Function name \"$functionName\" is prefixed with a package name but does not begin with a capital letter";
                $phpcsFile->addError($error, $stackPtr);
                return;
            }
        }

        // If it doesn't have a camel caps part, it's not valid.
        if (!$this->isLambda($functionName) && trim($camelCapsPart) === '') {
            $error = "Function name \"$functionName\" is not valid; name appears incomplete";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        $validName = true;
        $newPackagePart = $packagePart;
        $newCamelCapsPart = $camelCapsPart;

        // Every function must have a camel caps part, so check that first.
        if (PHP_CodeSniffer::isCamelCaps($camelCapsPart, false, true, false) === false) {
            $validName = false;
            $newCamelCapsPart = strtolower($camelCapsPart{0}) . substr($camelCapsPart, 1);
        }

        if ($packagePart !== '') {
            // Check that each new word starts with a capital.
            $nameBits = explode('_', $packagePart);
            foreach ($nameBits as $bit) {
                if ($bit{0} !== strtoupper($bit{0})) {
                    $newPackagePart = '';
                    foreach ($nameBits as $bit) {
                        $newPackagePart .= strtoupper($bit{0}) . substr($bit, 1) . '_';
                    }

                    $validName = false;
                    break;
                }
            }
        }

        if (!$this->isLambda($functionName) && $validName === false) {
            $newName = rtrim($newPackagePart, '_') . '_' . $newCamelCapsPart;
            if ($newPackagePart === '') {
                $newName = $newCamelCapsPart;
            } else {
                $newName = rtrim($newPackagePart, '_') . '_' . $newCamelCapsPart;
            }

            $error = "Function name \"$functionName\" is invalid; consider \"$newName\" instead";
            $phpcsFile->addError($error, $stackPtr);
        }

    }
    //end processTokenOutsideScope()
}

//end class

?>
