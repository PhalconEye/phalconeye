<?php

namespace Main\Migration;

use Core\Model\LanguageModel;
use Engine\Migration\AbstractMigration;

class Migration_20161101_132702 extends AbstractMigration
{
    function run()
    {
        $table = LanguageModel::getTableName();
        $this->execute("INSERT IGNORE INTO `{$table}` (name, language, locale) VALUES ('xxx', 'yy', 'ru_RU')");

        throw new \Exception('test');
    }
}