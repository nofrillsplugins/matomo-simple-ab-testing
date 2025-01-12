<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting\Columns;

use Piwik\Columns\Dimension;

class ExperimentCategory extends Dimension
{
    protected $nameSingular = 'SimpleABTesting_ExperimentCategory';
    protected $namePlural = 'SimpleABTesting_ExperimentCategories';
    protected $segmentName = 'experimentCategory';
    protected $category = 'SimpleABTesting_Experiment';
    protected $dbTableName = 'simple_ab_testing_log';
    protected $columnName = 'category';
    protected $sqlSegment = 'simple_ab_testing_log.category';
}
