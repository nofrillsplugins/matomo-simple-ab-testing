<?php

namespace Piwik\Plugins\SimpleABTesting\Columns;

use Piwik\Common;
use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Tracker\Request;

class ExperimentName extends VisitDimension
{
    protected $columnName = 'sabt_experiment_name';
    protected $columnType = 'VARCHAR(255) NULL';
    protected $nameSingular = 'Experiment Name'; // Visible in reports.
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
        return false; // Don't overwrite experiment name for existing visits.
    }
}