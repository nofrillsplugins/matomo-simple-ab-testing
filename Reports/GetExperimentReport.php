<?php

namespace Piwik\Plugins\SimpleABTesting\Reports;

use Piwik\Piwik;
use Piwik\Plugins\SimpleABTesting\Reports\Base;

class GetExperimentReport extends Base
{
    protected function init()
    {
        parent::init();
        // Set up where the report will appear in the UI
        //$this->categoryId = 'SimpleABTesting'; // "General" category in the reports menu
        $this->subcategoryId = 'SimpleABTesting_Report'; // Subcategory, you can define this or leave it empty

        // Report name and order in the menu
        $this->name = Piwik::translate('SimpleABTesting_ExperimentReport');
        $this->order = 10; // Determines the order relative to other reports

        // The dimension and metrics for the report
        $this->dimension = null; // No specific dimension for this report
        $this->metrics = array(
          'nb_visits' => Piwik::translate('General_ColumnNbVisits'),
          'nb_actions' => Piwik::translate('General_ColumnNbActions'),
        );

        // Specify which processed metrics (e.g., visit duration, bounce rate) to include
        $this->processedMetrics = false;

        // Specify the custom dimension columns you want to add (not required for all reports)
        $this->parameters = array();
    }

    public function getMetrics()
    {
        // Define the metrics for the report
        return array(
          'nb_visits' => Piwik::translate('General_ColumnNbVisits'),
          'sabt_is_variant' => Piwik::translate('SimpleABTesting_ColumnIsVariant'), // Matches "is_variant"
          'sabt_count' => Piwik::translate('SimpleABTesting_Count'), // Matches "is_variant"
        );
    }

    public function getRelatedReports()
    {
        // Define related reports, such as other A/B testing reports
        return array(); // No related reports in this case
    }

    public function isEnabled()
    {
        // Return true to ensure the report is enabled
        return true;
    }
}
