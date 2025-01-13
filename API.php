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
use Piwik\Plugins\SimpleABTesting\Dao\Experiments;
use Piwik\Container\StaticContainer;
use Piwik\ArchiveProcessor\Parameters;
use Piwik\API\Request;
use Piwik\DataTable;
use Piwik\Archive;
use Piwik\ArchiveProcessor;

/**
 * API for plugin RebelNotifications. With this you can handle notifications to
 * your users of Matomo through the API. Delete, Update, Edit etc.
 *
 * @method static \Piwik\Plugins\RebelNotifications\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @var Experiments
     */
    private $experiments;

    public function __construct()
    {
        $this->experiments = StaticContainer::get(Experiments::class);
    }
    /**
     * Add an experiment
     */
    public function insertExperiment(bool $idSite, string $name, string $hypothesis, string $description, string $fromDate, string $toDate, string $cssInsert, string $customJs): void
    {
        Piwik::checkUserHasSomeAdminAccess();
        $this->experiments->insertExperiment($idSite, $name, $hypothesis, $description, $fromDate, $toDate, $cssInsert, $customJs);
    }

    public function deleteExperiment(bool $id): void
    {
        Piwik::checkUserHasSomeAdminAccess();
        $this->experiments->deleteExperiment($id);
    }

    /**
     * Get report data for the experiment report (calls API endpoint).
     */
    public function getExperimentReportData($idSite, $period, $date)
    {
        Piwik::checkUserHasViewAccess($idSite);
        // SQL query to fetch experiment report data
        $sql = "
            SELECT
                experiment_name AS `experiment_name`,
                COUNT(DISTINCT idvisitor) AS `nb_unique_visitors`,
                COUNT(*) AS `nb_visits`
            FROM " . Common::prefixTable('simple_ab_testing_log') . "
            WHERE idsite = ?
            GROUP BY experiment_name
        ";

        // Fetch and return data
        return Db::fetchAll($sql, [$idSite]);
    }

    /**
     * Fetch experiment data from the archive blobs for reports or APIs.
     *
     * @param int $idSite The site ID.
     * @param string $period The period (e.g., 'day', 'week', 'month').
     * @param string $date The date range (e.g., 'today', 'last7', '2024-01-01').
     * @param string|null $segment The segment string (optional, default is null).
     * @return DataTable The archived experiment data grouped by experiment_name.
     */
    public function getExperimentData(int $idSite, string $period, string $date, string $segment = null): DataTable
    {
        // Use Matomo's Archive to fetch the archived data
        //Piwik::checkUserHasViewAccess($idSite);
        $dataTable = Archive::createDataTableFromArchive(
            Archiver::RECORD_NAME,
            $idSite,
            $period,
            $date,
            $segment
        );
       // $data = gzuncompress(hex2bin('789C4BB432B0AAAE0500064F01FE'));
        return $dataTable;
    }
}
