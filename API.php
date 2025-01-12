<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Db;
use Exception;
use Piwik\Plugins\SimpleABTesting\Dao\Experiments;
use Piwik\Container\StaticContainer;

/**
 * API for plugin RebelNotifications. With this you can handle notifications to
 * your users of Matomo through the API. Delete, Update, Edit etc.
 *
 * @method static \Piwik\Plugins\RebelNotifications\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @var Experiments
     */
    private $experiments;

    public function __construct()
    {
        $this->experiments = StaticContainer::get(Experiments::class);
    }
    /**
     * Add an experiment
     */
    public function insertExperiment(bool $idSite, string $name, string $hypothesis, string $description, string $fromDate, string $toDate, string $cssInsert, string $customJs): void
    {
        Piwik::checkUserHasSomeAdminAccess();
        $this->experiments->insertExperiment($idSite, $name, $hypothesis, $description, $fromDate, $toDate, $cssInsert, $customJs);
    }

    public function deleteExperiment(bool $id): void
    {
        Piwik::checkUserHasSomeAdminAccess();
        $this->experiments->deleteExperiment($id);
    }
}
