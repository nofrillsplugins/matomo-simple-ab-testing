<?php

namespace Piwik\Plugins\SimpleABTesting\Tracker;

use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Log\LoggerInterface;

class ActionExperiment extends Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    const TYPE_EXPERIMENT = 920;

    protected $eventValue;

    public function __construct(Request $request, LoggerInterface $logger)
    {
        parent::__construct(Action::TYPE_EVENT, $request);
        $sabt = $request->getParam('sabt');
        $this->setActionUrl($sabt);
        $this->logger = $logger;
        $this->eventValue = self::getEventValue($request);
        $this->logger->warning($request);
    }

    public static function shouldHandle(Request $request)
    {
          $sabt = $request->getParam('sabt');
          $experiment = $request->getParam('experiment');
          return (strlen($sabt) > 0 && strlen($experiment) > 0);
    }

    protected function getActionsToLookup()
    {
        $this->logger->warning("Getting a request in getActionsToLookup");
        return [];
    }
    public static function getEventValue(Request $request)
    {
        return trim($request->getParam('sabt'));
    }
}
