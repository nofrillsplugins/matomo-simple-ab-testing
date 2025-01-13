<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\DataTable;
use Piwik\Plugin\Archiver as MatomoArchiver;
use Piwik\Db;
use Piwik\Log\LoggerInterface;
use Piwik\Container\StaticContainer;

class Archiver extends MatomoArchiver
{
    const RECORD_NAME = 'SimpleABTesting_ExperimentData';
    const DIMENSION = 'experiment_name';

    public function aggregateDayReport()
    {
        // Logger for debugging
        /** @var LoggerInterface $logger */
        $logger = StaticContainer::get('Psr\Log\LoggerInterface');
        $logger->debug('SimpleABTesting: Starting day report aggregation.');

        // Step 1: Retrieve archiving parameters from the processor
        $params = $this->getProcessor()->getParams();
        $idSite  =$this->getProcessor()->getParams()->getSite()->getId();
        $dateStart = $params->getDateStart()->toString('Y-m-d 00:00:00');
        $dateEnd = $params->getDateEnd()->toString('Y-m-d 23:59:59');

        $logger->debug("SimpleABTesting: Archiving params: idSite={$idSite}, dateStart={$dateStart}, dateEnd={$dateEnd}");

        $query = "
            SELECT
                experiment_name AS label,
                COUNT(*) AS nb_visits,
                COUNT(DISTINCT idvisitor) AS nb_unique_visitors
            FROM " . Common::prefixTable('simple_ab_testing_log') . "
            WHERE idsite = ?
            AND server_time BETWEEN ? AND ?
            GROUP BY experiment_name
        ";

        $logger->debug("SimpleABTesting: Running query: {$query}");

        // Execute the query
        $rows = Db::fetchAll($query, [$idSite, $dateStart, $dateEnd]);

        if (empty($rows)) {
            // Warn if no rows are fetched
            $logger->warning("SimpleABTesting: No rows fetched for site: {$idSite}");
        } else {
            $logger->debug('SimpleABTesting: Rows fetched: ' . print_r($rows, true));
        }

        // Step 3: Convert SQL rows to DataTable
        $dataTable = new DataTable();
        foreach ($rows as $row) {
            $dataTable->addRowFromSimpleArray([
                'label' => $row['label'],
                'nb_visits' => $row['nb_visits'],
                'nb_unique_visitors' => $row['nb_unique_visitors'],
            ]);
        }

        // Step 4: Save the DataTable in archive records as a blob
        $this->getProcessor()->insertBlobRecord(self::RECORD_NAME, $dataTable->getSerialized());
    }

    /**
     * Aggregate data across multiple periods (e.g., week, month, year).
     */
    public function aggregateMultipleReports()
    {
        $this->getProcessor()->aggregateDataTableRecords([self::RECORD_NAME]);
    }
}