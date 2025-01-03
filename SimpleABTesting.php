<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Menu\MenuTop;
use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\SimpleABTesting\Generator;

class SimpleABTesting extends \Piwik\Plugin
{
    public function install()
    {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('ab_tests') . " (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `idsite` int(11) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `hypothesis` text,
                    `description` text,
                    `domain` varchar(255) NOT NULL,
                    `from_date` date NOT NULL,
                    `to_date` date NOT NULL,
                    `url_regex` text,
                    `css_insert` text,
                    `js_insert` text,
                    `custom_dimension` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_name` (`name`)
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);

            $generator = new Generator();
            $generator->regenerateJS();
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    public function uninstall()
    {
        Db::dropTables(Common::prefixTable('ab_tests'));
    }
}
