<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Db;
use Exception;

/**
 * API for plugin RebelNotifications. With this you can handle notifications to
 * your users of Matomo through the API. Delete, Update, Edit etc.
 *
 * @method static \Piwik\Plugins\RebelNotifications\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Add an experiment
     */
    public function insertExperiment($idSite, $name, $hypothesis, $description, $fromDate, $toDate, $cssInsert, $customJs): void
    {

        Piwik::checkUserHasSomeAdminAccess();
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

    public function deleteExperiment($id): void
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
}
