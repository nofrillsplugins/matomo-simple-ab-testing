<?php

namespace Piwik\Plugins\SimpleABTesting\Reports;

use Piwik\Plugin\Report;
use Piwik\Piwik;

abstract class Base extends Report
{
    protected function init()
    {
        $this->categoryId = 'SimpleABTesting_SimpleABTesting'; // Report category
    }
}
