<?php

namespace Piwik\Plugins\SimpleABTesting\Reports;

use Piwik\Plugin\Report;

abstract class Base extends Report
{
    protected function init()
    {
        $this->categoryId = 'SimpleABTesting_SimpleABTesting';
    }
}
