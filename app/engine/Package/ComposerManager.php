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

use Composer\Composer;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Engine\Exception;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Composer factory.
 * Extracts current composer.phar and return included loaded class of composer.
 *
 * @category  PhalconEye
 * @package   Engine\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ComposerManager
{
    const
        /**
         * Composer.phar path.
         */
        COMPOSER_FILE_PATH = '/composer.phar',

        /**
         * Composer cache directory.
         */
        COMPOSER_CACHE_DIRECTORY = '/app/var/cache/composer/',

        /**
         * File that contains md5 hash of current composer file.
         * In case if composer will be updated - cache files will be rewritten.
         */
        COMPOSER_MD5_FILE = "composer.md5.check",

        /**
         * Composer bootstrap file.
         */
        COMPOSER_BOOTSTRAP_FILE = "src/bootstrap.php";

    /**
     * Composer object.
     *
     * @var Composer
     */
    protected $_composer;

    /**
     * Create composer manager.
     *
     * @param Composer $composer Composer object.
     */
    public function __construct(Composer $composer)
    {
        $this->_composer = $composer;
    }

    /**
     * Create composer object.
     * Extract composer.phar if required.
     *
     * @return ComposerManager
     *
     * @throws PackageException
     */
    public static function factory()
    {
        $composerPath = ROOT_PATH . self::COMPOSER_FILE_PATH;
        $composerCachePath = ROOT_PATH . self::COMPOSER_CACHE_DIRECTORY;
        $composerMD5File = $composerCachePath . self::COMPOSER_MD5_FILE;
        $composerBootstrap = $composerCachePath . self::COMPOSER_BOOTSTRAP_FILE;

        if (!file_exists($composerPath)) {
            throw new PackageException("Missing composer.phar... Please install it to: %s", $composerPath);
        }

        $currentComposerMD5 = md5_file($composerPath);
        $cachedComposerMD5 = '';

        if (file_exists($composerMD5File)) {
            $cachedComposerMD5 = file_get_contents($composerMD5File);
        }

        // Check if need to extract phar again.
        if ($currentComposerMD5 != $cachedComposerMD5) {
            // Cleanup first.
            Utilities::fsRmdirRecursive($composerCachePath, false, ['.gitignore']);

            // Extract.
            $phar = new \Phar(ROOT_PATH . self::COMPOSER_FILE_PATH);
            $phar->extractTo($composerCachePath);

            // Save MD5.
            file_put_contents($composerMD5File, $currentComposerMD5);
        }

        include_once $composerBootstrap;

        // Specific preparations for composer launch.
        chdir('../');
        putenv('HOME=' . exec('echo ~/'));
        putenv('COMPOSER=' . ROOT_PATH . '/composer.json');
        $_SERVER['argv'] = [];

        $IO = new ConsoleIO(new ArgvInput(), new NullOutput(), new HelperSet([]));
        $composer = Factory::create($IO);
        return new static($composer);
    }

    /**
     * Get composer object.
     *
     * @return Composer
     */
    public function getComposer()
    {
        return $this->_composer;
    }

    /**
     * Search package in repositories.
     *
     * @param string $name     Package name.
     * @param bool   $onlyName Search only in name?
     *
     * @return array|mixed
     */
    public function searchPackage($name, $onlyName = false)
    {
        $composer = $this->getComposer();

        $platformRepo = new PlatformRepository();
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();
        $installedRepo = new CompositeRepository(array($localRepo, $platformRepo));
        $repos = new CompositeRepository(
            array_merge(array($installedRepo), $composer->getRepositoryManager()->getRepositories())
        );

        $flags = $onlyName ? RepositoryInterface::SEARCH_NAME : RepositoryInterface::SEARCH_FULLTEXT;
        return $repos->search($name, $flags);
    }

    /**
     * Get installed packages.
     *
     * @return array
     */
    public function getInstalledPackages()
    {
        $repo = $this->getComposer()->getRepositoryManager()->getLocalRepository();
        return $repo->getPackages();
    }
}