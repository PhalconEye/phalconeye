<?php
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2013 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
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

namespace Engine\Exception;

use Engine\Behaviour\DIBehaviour;

/**
 * Prints exception/errors backtraces using a pretty visualization.
 *
 * @category  PhalconEye
 * @package   Engine\Exception
 * @author    Andres Gutierrez <andres@phalconphp.com>
 * @author    Eduar Carvajal <eduar@phalconphp.com>
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2011-2013 Phalcon Team
 * @copyright 2011-2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconphp.com/
 * @link      http://phalconeye.com/
 */
class PrettyExceptions
{
    use DIBehaviour;

    /**
     * Print the backtrace.
     *
     * @var bool
     */
    protected $_showBackTrace = true;

    /**
     * Show the application's code.
     *
     * @var bool
     */
    protected $_showFiles = true;

    /**
     * Show only the related part of the application.
     *
     * @var bool
     */
    protected $_showFileFragment = false;

    /**
     * CSS theme.
     *
     * @var string
     */
    protected $_theme = 'default';

    /**
     * Pretty Exceptions.
     *
     * @var string
     */
    protected $_uri = '/pretty-exceptions/';

    /**
     * Flag to control that only one exception/error is show at time
     */
    static protected $_showActive = false;

    /**
     * Set if the application's files must be opened an showed as part of the backtrace.
     *
     * @param boolean $showFiles Flag to show files.
     *
     * @return $this
     */
    public function showFiles($showFiles)
    {
        $this->_showFiles = $showFiles;
        return $this;
    }

    /**
     * Set if only the file fragment related to the exception must be shown instead of the complete file.
     *
     * @param boolean $showFileFragment Show flag.
     *
     * @return $this
     */
    public function showFileFragment($showFileFragment)
    {
        $this->_showFileFragment = $showFileFragment;
        return $this;
    }

    /**
     * Change the base uri for css/javascript sources.
     *
     * @param string $uri Base uri.
     *
     * @return $this
     */
    public function setBaseUri($uri)
    {
        $this->_uri = $uri;
        return $this;
    }

    /**
     * Get base uri.
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->getDI()->get('url')->get($this->_uri);
    }

    /**
     * Change the CSS theme.
     *
     * @param string $theme Theme name.
     *
     * @return $this
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Set if the exception/error backtrace must be shown.
     *
     * @param boolean $showBackTrace Show flag.
     *
     * @return $this
     */
    public function showBackTrace($showBackTrace)
    {
        $this->_showBackTrace = $showBackTrace;
        return $this;
    }

    /**
     * Returns the css sources.
     *
     * @return string
     */
    public function getCssSources()
    {
        return '<link href="' . $this->getBaseUri() . 'themes/' .
        $this->_theme . '.css" type="text/css" rel="stylesheet" />';
    }

    /**
     * Returns the javascript sources.
     *
     * @return string
     */
    public function getJsSources()
    {
        return '
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script type="text/javascript" src="' . $this->getBaseUri() . 'prettify/prettify.js"></script>
		<script type="text/javascript" src="' . $this->getBaseUri() . 'js/pretty.js"></script>
		<script type="text/javascript" src="' . $this->getBaseUri() . 'js/jquery.scrollTo-min.js"></script>';
    }

    /**
     * Returns the current framework version.
     *
     * @return string
     */
    public function getVersion()
    {
        if (class_exists("\Phalcon\Version")) {
            $version = \Phalcon\Version::get();
        } else {
            $version = "git-master";
        }
        $parts = explode(' ', $version);
        return '<div class="version">
			Phalcon Framework <a target="_new" href="http://docs.phalconphp.com/en/' . $parts[0] . '/">' . $version . '</a>
		</div>';
    }

    /**
     * Escape string.
     *
     * @param string $value The value.
     *
     * @return string
     */
    protected function _escapeString($value)
    {
        $value = str_replace("\n", "\\n", $value);
        $value = htmlentities($value, ENT_COMPAT, 'utf-8');
        return $value;
    }

    /**
     * Dump array.
     *
     * @param array $argument An array to dump.
     * @param int   $n        How deep?
     *
     * @return int|string
     */
    protected function _getArrayDump($argument, $n = 0)
    {
        if ($n < 3 && count($argument) > 0 && count($argument) < 8) {
            $dump = array();
            foreach ($argument as $k => $v) {
                if (is_scalar($v)) {
                    if ($v === '') {
                        $dump[] = $k . ' => (empty string)';
                    } else {
                        $dump[] = $k . ' => ' . $this->_escapeString($v);
                    }
                } else {

                    if (is_array($v)) {
                        $dump[] = $k . ' => Array(' . $this->_getArrayDump($v, $n + 1) . ')';
                        continue;
                    }

                    if (is_object($v)) {
                        $dump[] = $k . ' => Object(' . get_class($v) . ')';
                        continue;
                    }

                    if (is_null($v)) {
                        $dump[] = $k . ' => null';
                        continue;
                    }

                    $dump[] = $k . ' => ' . $v;
                }
            }
            return join(', ', $dump);
        }
        return count($argument);
    }

    /**
     * Shows a backtrace item.
     *
     * @param int   $n     Count.
     * @param array $trace Trace result.
     *
     * @return void
     */
    protected function _showTraceItem($n, $trace)
    {
        echo '<tr><td align="right" valign="top" class="error-number">#', $n, '</td><td>';
        if (isset($trace['class'])) {
            if (preg_match('/^Phalcon/', $trace['class'])) {
                echo '<span class="error-class"><a target="_new" href="http://docs.phalconphp.com/en/latest/api/',
                str_replace('\\', '_', $trace['class']), '.html">', $trace['class'], '</a></span>';
            } else {
                $classReflection = new \ReflectionClass($trace['class']);
                if ($classReflection->isInternal()) {
                    echo '<span class="error-class"><a target="_new" href="http://php.net/manual/en/class.',
                    str_replace('_', '-', strtolower($trace['class'])), '.php">', $trace['class'], '</a></span>';
                } else {
                    echo '<span class="error-class">', $trace['class'], '</span>';
                }
            }
            echo $trace['type'];
        }

        if (isset($trace['class'])) {
            echo '<span class="error-function">', $trace['function'], '</span>';
        } else {
            if (function_exists($trace['function'])) {
                $functionReflection = new \ReflectionFunction($trace['function']);
                if ($functionReflection->isInternal()) {
                    echo '<span class="error-function"><a target="_new" href="http://php.net/manual/en/function.',
                    str_replace('_', '-', $trace['function']), '.php">', $trace['function'], '</a></span>';
                } else {
                    echo '<span class="error-function">', $trace['function'], '</span>';
                }
            } else {
                echo '<span class="error-function">', $trace['function'], '</span>';
            }
        }

        if (isset($trace['args'])) {
            $this->_echoArgs($trace['args']);
        }

        if (isset($trace['file'])) {
            echo '<br/><span class="error-file">', $trace['file'], ' (', $trace['line'], ')</span>';
        }

        echo '</td></tr>';

        if ($this->_showFiles) {
            if (isset($trace['file'])) {
                $this->_echoFile($trace['file'], $trace['line']);

            }
        }
    }

    /**
     * Echo error arguments.
     *
     * @param array $args Arguments.
     *
     * @return void
     */
    protected function _echoArgs($args)
    {
        $arguments = array();
        foreach ($args as $argument) {
            if (is_scalar($argument)) {

                if (is_bool($argument)) {
                    if ($argument) {
                        $arguments[] = '<span class="error-parameter">true</span>';
                    } else {
                        $arguments[] = '<span class="error-parameter">null</span>';
                    }
                    continue;
                }

                if (is_string($argument)) {
                    $argument = $this->_escapeString($argument);
                }

                $arguments[] = '<span class="error-parameter">' . $argument . '</span>';
            } else {
                if (is_object($argument)) {
                    if (method_exists($argument, 'dump')) {
                        $arguments[] = '<span class="error-parameter">Object(' .
                            get_class($argument) . ': ' . $this->_getArrayDump($argument->dump()) . ')</span>';
                    } else {
                        $arguments[] = '<span class="error-parameter">Object(' . get_class($argument) . ')</span>';
                    }
                } else {
                    if (is_array($argument)) {
                        $arguments[] = '<span class="error-parameter">Array(' .
                            $this->_getArrayDump($argument) . ')</span>';
                    } else {
                        if (is_null($argument)) {
                            $arguments[] = '<span class="error-parameter">null</span>';
                            continue;
                        }
                    }
                }
            }
        }
        echo '(' . join(', ', $arguments) . ')';
    }

    /**
     * Show files data.
     *
     * @param string $file File name.
     * @param int    $line Line number.
     *
     * @return void
     */
    protected function _echoFile($file, $line)
    {
        echo '</table>';
        $lines = file($file);

        if ($this->_showFileFragment) {
            $numberLines = count($lines);
            $firstLine = ($line - 7) < 1 ? 1 : $line - 7;
            $lastLine = ($line + 5 > $numberLines ? $numberLines : $line + 5);
            echo "<pre class='prettyprint highlight:" . $firstLine . ":" . $line . " linenums:" .
                $firstLine . "'>";
        } else {
            $firstLine = 1;
            $lastLine = count($lines) - 1;
            echo "<pre class='prettyprint highlight:" . $firstLine . ":" . $line . " linenums error-scroll'>";
        }

        for ($i = $firstLine; $i <= $lastLine; ++$i) {

            if ($this->_showFileFragment) {
                if ($i == $firstLine) {
                    if (preg_match('#\*\/$#', rtrim($lines[$i - 1]))) {
                        $lines[$i - 1] = str_replace("* /", "  ", $lines[$i - 1]);
                    }
                }
            }

            if ($lines[$i - 1] != PHP_EOL) {
                $lines[$i - 1] = str_replace("\t", "  ", $lines[$i - 1]);
                echo htmlentities($lines[$i - 1], ENT_COMPAT, 'UTF-8');
            } else {
                echo '&nbsp;' . "\n";
            }
        }
        echo '</pre>';
        echo '<table cellspacing="0">';
    }

    /**
     * Handles exceptions.
     *
     * @param \Exception $e Exception object.
     *
     * @return boolean
     */
    public function handleException($e)
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (self::$_showActive) {
            echo $e->getMessage();
            return;
        }

        self::$_showActive = true;

        echo '<html><head><title>Exception - ',
        get_class($e),
        ': ',
        $e->getMessage(),
        '</title>',
        $this->getCssSources(), '</head><body>';

        echo '<div class="error-main">
			', get_class($e), ': ', $e->getMessage(), '
			<br/><span class="error-file">', $e->getFile(), ' (', $e->getLine(), ')</span>
		</div>';

        if ($this->_showBackTrace) {
            echo '<div class="error-backtrace"><table cellspacing="0">';
            foreach ($e->getTrace() as $n => $trace) {
                $this->_showTraceItem($n, $trace);
            }
            echo '</table></div>';
        }

        echo $this->getVersion();
        echo $this->getJsSources() . '</body></html>';
        self::$_showActive = false;

        return true;
    }

    /**
     * Handles errors/warnings/notices.
     *
     * @param int    $errorCode    PHP error code.
     * @param string $errorMessage Related message.
     * @param string $errorFile    In what file.
     * @param int    $errorLine    In what line.
     *
     * @return bool
     */
    public function handleError($errorCode, $errorMessage, $errorFile, $errorLine)
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (self::$_showActive) {
            echo $errorMessage;
            return false;
        }

        if (!(error_reporting() & $errorCode)) {
            return false;
        }

        self::$_showActive = true;

        header("Content-type: text/html");

        echo '<html><head><title>Exception - ', $errorMessage, '</title>', $this->getCssSources(), '</head><body>';

        echo '<div class="error-main">
			', $errorMessage, '
			<br/><span class="error-file">', $errorFile, ' (', $errorLine, ')</span>
		</div>';

        if ($this->_showBackTrace) {
            echo '<div class="error-backtrace"><table cellspacing="0">';
            foreach (debug_backtrace() as $n => $trace) {
                if ($n == 0) {
                    continue;
                }
                $this->_showTraceItem($n, $trace);
            }
            echo '</table></div>';
        }

        echo $this->getVersion();
        echo $this->getJsSources() . '</body></html>';
        self::$_showActive = false;

        return true;
    }
}