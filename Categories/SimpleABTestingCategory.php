<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting\Categories;

use Piwik\Category\Category;

class SimpleABTestingCategory extends Category
{
    protected $id = 'SimpleABTesting_SimpleABTesting';
    protected $order = 99;
    protected $icon = 'simpleabtestingicon-fire';
}
