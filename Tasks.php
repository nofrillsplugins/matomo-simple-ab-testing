<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Plugins\SimpleABTesting\Generator;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule()
    {
        $this->hourly('myTaskWithParam', 'anystring');
    }

    public function myTaskWithParam($param)
    {
        $generator = new Generator;
        $generator->regenerateJS();
    }
}
