<?php

namespace Piwik\Plugins\SimpleABTesting\Reports;

use Piwik\Piwik;
use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\SimpleABTesting\Columns\ExperimentName;

class GetExperimentReport extends Base
{
    protected function init()
    {
        parent::init();

        $this->name = Piwik::translate('SimpleABTesting_ExperimentsReport');
        $this->dimension = new ExperimentName();
        $this->metrics = [
            'nb_visits' => Piwik::translate('SimpleABTesting_NbVisits'),
            'nb_unique_visitors' => Piwik::translate('SimpleABTesting_NbUniqueVisitors'),
        ];
        $this->processedMetrics = [];
        $this->order = 1;
        $this->subcategoryId = $this->name;
        $this->documentation = Piwik::translate('SimpleABTesting_ReportHelpText');

    }

    /**
     * @param ViewDataTable $view
     */
    public function configureView(ViewDataTable $view)
    {
        $view->config->show_table = true;
        $view->config->title = $this->name;
        $view->config->columns_to_display = [
            'label', // Experiment name
            'nb_visits',
            'nb_unique_visitors',
        ];

        $view->config->translations['label'] = Piwik::translate('SimpleABTesting_ExperimentName');
        $view->config->translations['nb_visits'] = Piwik::translate('SimpleABTesting_NbVisits');
        $view->config->translations['nb_unique_visitors'] = Piwik::translate('SimpleABTesting_NbUniqueVisitors');
        $view->config->show_footer_message = true;
    }

}
