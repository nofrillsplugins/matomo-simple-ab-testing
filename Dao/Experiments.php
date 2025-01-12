<?php

namespace Piwik\Plugins\SimpleABTesting\Dao;

use Piwik\Common;
use Piwik\Db;
use Exception;

class Experiments
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

    public function insertExperiment(bool $idSite, string $name, string $hypothesis, string $description, string $fromDate, string $toDate, string $cssInsert, string $customJs)
    {
        $query = "INSERT INTO `" . Common::prefixTable('simple_ab_testing_experiments') .
        "` (idsite, name, hypothesis, description, from_date, to_date, css_insert, js_insert) " .
        "VALUES (?,?,?,?,?,?,?,?)";
        $params = [
        $idSite,
        $name,
        $hypothesis,
        $description,
        $fromDate,
        $toDate,
        $cssInsert,
        $customJs
        ];
        try {
            $db = $this->getDb();
            $db->query($query, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteExperiment(bool $id): void
    {
        $query = "DELETE FROM `" . Common::prefixTable('simple_ab_testing_experiments') . "` WHERE id = ?";
        $params = [$id];
        try {
            $db = $this->getDb();
            $db->query($query, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function getDb()
    {
        return Db::get();
    }

    public function uninstall()
    {
        Db::dropTables(Common::prefixTable('simple_ab_testing_experiments'));
    }
}
