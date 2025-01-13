<?php

namespace Piwik\Plugins\SimpleABTesting\Columns;

use Piwik\Columns\Dimension;
use Piwik\Common;

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

    public function getExpression()
    {
        return Common::prefixTable($this->sqlSegment); // Matching column in the database table
    }
}
