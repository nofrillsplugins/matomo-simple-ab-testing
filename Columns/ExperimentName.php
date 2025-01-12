<?php

namespace Piwik\Plugins\SimpleABTesting\Columns;

use Piwik\Common;
use Piwik\Tracker\Request;
use Piwik\Columns\Dimension;

class ExperimentName extends Dimension
{
    protected $columnName = 'experiment_name';
    protected $columnType = 'VARCHAR(255) NULL';
    protected $nameSingular = 'SimpleABTesting_Experiment';
    protected $namePlural = 'SimpleABTesting_Experiments';
    protected $dbTableName = 'simple_ab_testing_log';
    protected $category = 'SimpleABTesting_Experiment';
    protected $sqlSegment = 'simple_ab_testing_log.experiment_name';
    protected $segmentName = 'experimentName';

    public function onNewVisit(Request $request, $visitor, $action)
    {
        // Extract the experiment name from the tracking request's "experiment" parameter
        $experimentName = Common::getRequestVar('experiment', '', 'string', $request->getParams());

        if (!empty($experimentName)) {
            return $experimentName;
        }
        return null; // No value to log if there's no experiment name.
    }

    public function onExistingVisit(Request $request, $visitor, $action)
    {
        // Extract the experiment name from the tracking request's "experiment" parameter
        $experimentName = Common::getRequestVar('experiment', '', 'string', $request->getParams());

        if (!empty($experimentName)) {
            return $experimentName;
        }
        return null; // No value to log if there's no experiment name.
    }
}
