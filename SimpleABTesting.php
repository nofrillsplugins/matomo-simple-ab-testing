<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\Db;
use Exception;

class SimpleABTesting extends \Piwik\Plugin
{
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
                    `custom_dimension` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_name` (`name`)
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    public function uninstall()
    {
        Db::dropTables(Common::prefixTable('simple_ab_testing_experiments'));
    }
}
