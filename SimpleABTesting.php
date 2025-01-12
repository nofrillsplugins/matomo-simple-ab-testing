<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\Db;
use Exception;
use Piwik\Plugin;

class SimpleABTesting extends Plugin
{
    public function isTrackerPlugin()
    {
        return true;
    }

    public function install()
    {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('simple_ab_testing_experiments') . " (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `idsite` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `hypothesis` text,
                    `description` text,
                    `from_date` date NOT NULL,
                    `to_date` date NOT NULL,
                    `css_insert` text,
                    `js_insert` text,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_name` (`name`)
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);
        }
        catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
        try {
        $sql = "CREATE TABLE " . Common::prefixTable('simple_ab_testing_log') . " (
        `idlog` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `idsite` INT UNSIGNED NOT NULL,
        `idvisit` BIGINT(10) UNSIGNED NOT NULL,
        `idvisitor` BINARY(8) NOT NULL,
        `count` INT DEFAULT NULL NULL,
        `experiment_name` VARCHAR(255) NULL,
        `variant` INT DEFAULT NULL NULL,
        `server_time` DATETIME NOT NULL,
        `created_time` DATETIME NOT NULL,
        `idpageview` CHAR(6) NULL DEFAULT NULL,
        `idaction_url` INTEGER UNSIGNED NULL,
        `idaction_name` INTEGER UNSIGNED NULL,
        `category` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`idlog`)
        )  DEFAULT CHARSET=utf8 ";
        Db::exec($sql);
        }
        catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    public function uninstall()
    {
        Db::dropTables(Common::prefixTable('simple_ab_testing_experiments'));
        Db::dropTables(Common::prefixTable('simple_ab_testing_log'));
    }
}
