<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Plugin;
use Piwik\Plugins\SimpleABTesting\Dao\LogExperiment;
use Piwik\Plugins\SimpleABTesting\Dao\Experiments;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\SimpleABTesting\Reports\GetExperimentReport;

class SimpleABTesting extends Plugin
{
    /**
     * @var LogExperiment
     */
    private $logExperiment;

    /**
     * @var Experiments
     */
    private $experiments;

    public function __construct()
    {
        parent::__construct();
        $this->logExperiment = StaticContainer::get(LogExperiment::class);
        $this->experiments = StaticContainer::get(Experiments::class);
    }
    public function isTrackerPlugin()
    {
        return true;
    }

    public function install()
    {
        $this->experiments->install();
        $this->logExperiment->install();
    }

    public function uninstall()
    {
        $this->experiments->uninstall();
        $this->logExperiment->uninstall();
    }

    public function registerReports(): array
    {
        return [
            new GetExperimentReport(), // Register your custom report
        ];
    }
}
