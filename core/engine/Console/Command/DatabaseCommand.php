<?php
/*
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconeye.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
 +------------------------------------------------------------------------+
*/

namespace Engine\Console\Command;

use Engine\Console\AbstractCommand;
use Engine\Console\CommandInterface;
use Engine\Console\ConsoleLogger;
use Engine\Db\Data\UpdateData;
use Engine\Db\Data\UpdateStatementData;
use Engine\Db\SchemaUpdater;
use Engine\Utils\ConsoleUtils;
use Phalcon\Validation\Validator\Identical;

/**
 * Database command.
 *
 * @category  PhalconEye
 * @package   Engine\Console\Commands
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @CommandName(['database', 'db'])
 * @CommandDescription('Database management.')
 */
class DatabaseCommand extends AbstractCommand implements CommandInterface
{
    /**
     * Initialize system: create schema, import core data, import sample data (if required).
     *
     * @param bool $importCoreData   Import core data from all modules.
     * @param bool $importSampleData Import sample data from all modules.
     * @param bool $confirm          Confirm initialization.
     */
    public function initializeAction(
        bool $importCoreData = true,
        bool $importSampleData = false,
        bool $confirm = false
    )
    {
        $this->_initLogger();
        if (!$confirm) {
            $this->_readline(
                "Write confirmation (type 'initialization'): ",
                [
                    new Identical(['value' => 'initialization'])
                ]
            );
        }

        // Test requirements.
        define('CHECK_REQUIREMENTS', true);
        require_once(ROOT_PATH . '/requirements.php');

        $schema = new SchemaUpdater($this->getDI(), $this->getConfig()->database->dbname);
        $result = $schema->initialize($importCoreData, $importSampleData);
        $this->_printUpdateData($result);

        // Install assets.
        print ConsoleUtils::info('================================================================') . PHP_EOL;
        print ConsoleUtils::info("| Installing assets") . PHP_EOL;
        print ConsoleUtils::info('================================================================') . PHP_EOL;
        $this->getAssets()->installAssets();
        print ConsoleUtils::info('Done!') . PHP_EOL;

        print  PHP_EOL . ConsoleUtils::successLine('Initialization completed') . PHP_EOL;
    }

    /**
     * Update database schema according to models metadata.
     *
     * @param string|null $model Model name to update. Example: \\Test\\Model\\ClassModel.
     *
     * @return void
     */
    public function updateAction($model = null)
    {
        $this->_initLogger();
        if (!$this->getRegistry()->initialized) {
            print ConsoleUtils::errorLine("System isn't initialized");
            return;
        }

        $schema = new SchemaUpdater($this->getDI(), $this->getConfig()->database->dbname);

        if (!$model) {
            $result = $schema->update();
        } else {
            $result = $schema->updateTable($model);
        }

        $this->_printUpdateData($result);
        print PHP_EOL . ConsoleUtils::successLine('Update Completed') . PHP_EOL;
    }

    /**
     * Cleanup database schema according to models metadata.
     *
     * @return void
     */
    public function cleanupAction()
    {
        $this->_initLogger();
        if (!$this->getRegistry()->initialized) {
            print ConsoleUtils::errorLine("System isn't initialized");
            return;
        }

        $schema = new SchemaUpdater($this->getDI(), $this->getConfig()->database->dbname);
        $result = $schema->cleanupDatabase();
        $this->_printUpdateData($result);
    }

    /**
     * Print update data.
     *
     * @param UpdateData[]|string $data Update data to print.
     */
    protected function _printUpdateData($data)
    {
        if (is_string($data)) {
            print ConsoleUtils::errorLine($data);
            print PHP_EOL;
            return;
        }

        $allStatementsCount = 0;
        $allFailedCount = 0;

        foreach ($data as $item) {
            $allStatementsCount += $item->getExecutedCount();
            $allFailedCount += $item->getFailedCount();
        }

        if ($allStatementsCount == 0) {
            print ConsoleUtils::successLine('Up to date');
        } else {
            print ConsoleUtils::command(
                'Executed statements:',
                $allStatementsCount,
                ConsoleUtils::FG_LIGHT_CYAN,
                ConsoleUtils::FG_GREEN,
                50
            );
            foreach ($data as $table => $item) {
                if ($item->getExecutedCount() == 0) {
                    continue;
                }
                print ConsoleUtils::command(
                    $table . ':',
                    $item->getExecutedCount(),
                    ConsoleUtils::FG_LIGHT_GREEN,
                    ConsoleUtils::FG_GREEN,
                    50
                );
            }

            if ($allFailedCount > 0) {
                print PHP_EOL;
                print ConsoleUtils::command(
                    'Failed statements:',
                    $allFailedCount,
                    ConsoleUtils::FG_RED,
                    ConsoleUtils::FG_GREEN,
                    50
                );

                foreach ($data as $table => $item) {
                    /** @var UpdateStatementData $stmt */
                    foreach ($item->getStatements() as $stmt) {
                        if ($stmt->getFailedCount() == 0) {
                            continue;
                        }

                        print ConsoleUtils::command(
                            $table . ':' .
                            ConsoleUtils::text('') . '[' . ucfirst(substr($stmt->getObj(), 0, 1)) . ']->' .
                            $stmt->getStmt
                            (),
                            $stmt->getFailedCount(),
                            ConsoleUtils::FG_LIGHT_RED,
                            ConsoleUtils::FG_GREEN,
                            50
                        );
                    }
                }
            }
        }

        print PHP_EOL;
    }

    /**
     * Initialize console logger.
     */
    private function _initLogger()
    {
        $this->getDI()->setShared('logger', new ConsoleLogger($this->getLogger()));
    }
}