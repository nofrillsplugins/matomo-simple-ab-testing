<?php

namespace Piwik\Plugins\SimpleABTesting\Tracker;

use Piwik\Date;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visit\VisitProperties;
use Piwik\Log\LoggerInterface;
use Piwik\Plugins\SimpleABTesting\Dao\LogExperiment;
use Piwik\Tracker\RequestProcessor as MatomoRequestProcessor;

class RequestProcessor extends MatomoRequestProcessor
{
    const TRACK_SABT = 'sabt';
    const TRACK_EXPERIMENT = 'experiment';

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
        // $params = $request->getParams();
        // $this->logger->warning("Getting a request in onExistingVisit");

    }

    public function recordLogs(VisitProperties $visitProperties, Request $request)
    {
        $this->logger->warning("Getting a request in recordLogs");
        $params = $request->getParams();
        if (empty($params[self::TRACK_SABT])) {
            return;
        }

        $message = $params[self::TRACK_SABT];
       // $request->getIdSite();

    }

}
