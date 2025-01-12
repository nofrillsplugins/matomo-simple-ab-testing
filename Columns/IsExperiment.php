<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting\Columns;

use Piwik\Common;
use Piwik\Columns\Dimension;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;

class IsExperiment extends Dimension
{
    /**
     * This will be the name of the column in the log_visit table if a $columnType is specified.
     * @var string
     */
    protected $columnName = 'variant';
    protected $columnType = 'INT DEFAULT NULL NULL';
    protected $nameSingular = 'SimpleABTesting_IsExperiment';
    protected $namePlural = 'SimpleABTesting_IsExperiments';
    protected $dbTableName = 'simple_ab_testing_log';
    protected $category = 'SimpleABTesting_IsExperiment';
    protected $sqlSegment = 'simple_ab_testing_log.variant';
    protected $segmentName = 'experimentVariant';

    /**
     * @var string
     */
    protected $acceptValues = 'Here you should explain which values are accepted/useful for segments: Any number, for instance 1, 2, 3 , 99';

    /**
     * The onNewVisit method is triggered when a new visitor is detected. This means here you can define an initial
     * value for this user. By returning boolean false no value will be saved. Once the user makes another action the
     * event "onExistingVisit" is executed. That means for each visitor this method is executed once. If you do not want
     * to perform any action on a new visit you can just remove this method.
     *
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     * @return mixed|false
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $paramValue = Common::getRequestVar('sabt', '', 'string', $request->getParams());
        if (isset($paramValue)) {
            return $paramValue;
        }
        return 0;

    }

    /**
     * The onExistingVisit method is triggered when a visitor was recognized meaning it is not a new visitor.
     * If you want you can overwrite any previous value set by the event onNewVisit. By returning boolean false no value
     * will be updated. If you do not want to perform any action on a new visit you can just remove this method.
     *
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     *
     * @return mixed|false
     */
    public function onExistingVisit(Request $request, Visitor $visitor, $action)
    {
        $paramValue = Common::getRequestVar('sabt', '', 'string', $request->getParams());
        if (isset($paramValue)) {
            return $paramValue;
        }
        return 0;
    }

    /**
     * This event is executed shortly after "onNewVisit" or "onExistingVisit" in case the visitor converted a goal.
     * In this example we give the user 5 extra points for this achievement. Usually this event is not needed and you
     * can simply remove this method therefore. An example would be for instance to persist the last converted
     * action url. Return boolean false if you do not want to change the current value.
     *
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     *
     * @return mixed|false
     */
    // public function onConvertedVisit(Request $request, Visitor $visitor, $action)
    // {
    //     return $visitor->getVisitorColumn($this->columnName) + 5;  // give this visitor 5 extra achievement points
    // }

    /**
     * By implementing this event you can persist a value to the log_conversion table in case a conversion happens.
     * The persisted value will be logged along the conversion and will not be changed afterwards.
     * This allows you to generate reports that shows for instance which url was called how often for a specific
     * conversion. Once you implement this event and a $columnType is defined a column in the log_conversion MySQL table
     * will be created automatically.
     *
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     *
     * @return mixed
    public function onAnyGoalConversion(Request $request, Visitor $visitor, $action)
    {
        return $visitor->getVisitorColumn($this->columnName);
    }
     */


}
