<?php

namespace Piwik\Plugins\SimpleABTesting\Tracker;

use Piwik\Date;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visit\VisitProperties;
use Piwik\Log\LoggerInterface;
use Piwik\Plugins\SimpleABTesting\Dao\LogExperiment;
use Piwik\Tracker\RequestProcessor as MatomoRequestProcessor;
use Piwik\Common;
use Piwik\Db;

class RequestProcessor extends MatomoRequestProcessor
{
    public const TRACK_SABT = 'sabt';
    public const TRACK_EXPERIMENT = 'experiment';

    /**
     * @var LogExperiment
     */
    private $logExperiment;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LogExperiment $logExperiment, LoggerInterface $logger)
    {
        $this->logExperiment = $logExperiment;
        $this->logger = $logger;
    }


    public function onExistingVisit(&$valuesToUpdate, VisitProperties $visitProperties, Request $request)
    {

        // @todo: add logic to update visits.
    }

    public function recordLogs(VisitProperties $visitProperties, Request $request)
    {

        $params = $request->getParams();
        if (empty($params[self::TRACK_SABT])) {
            return;
        }

        $props = [
          'idsite' => $request->getIdSite(),
          'experiment_name' => $params[self::TRACK_EXPERIMENT] ?? 'Unknown',
          'variant' => $params[self::TRACK_SABT] ?? 0,
          'idvisit' => $visitProperties->getProperty('idvisit'),
          'idvisitor' => $visitProperties->getProperty('idvisitor'),
          'server_time' => date('Y-m-d H:i:s', $request->getCurrentTimestamp()),
          'created_time' => date('Y-m-d H:i:s', time()),
          'category' => 'Experiment',
          'idaction_url' => $visitProperties->getProperty('visit_exit_idaction_url') ?? $visitProperties->getProperty('visit_entry_idaction_url'),
          'idaction_name' => $visitProperties->getProperty('visit_exit_idaction_name') ?? $visitProperties->getProperty('visit_entry_idaction_name'),
        ];
        $this->logExperiment->createLog($props);
    }
}
