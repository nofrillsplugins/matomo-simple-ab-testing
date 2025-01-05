<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Piwik;
use Piwik\Common;
use Piwik\Url;
use Piwik\Plugins\SimpleABTesting\API;
use Piwik\Plugins\SimpleABTesting\Helpers;
use Piwik\Request;

class Controller extends \Piwik\Plugin\Controller
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();

        if (!Piwik::isUserHasSomeAdminAccess()) {
            echo "Not allowed!";
            exit();
        }
    }

    /**
     * Add an experiment
     */
    public function addExperiment()
    {
        $this->securityChecks();

        $name = trim(Request::fromRequest()->getStringParameter('name', 'string'));
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        $hypothesis = trim(Request::fromRequest()->getStringParameter('hypothesis', 'string'));
        $description = trim(Request::fromRequest()->getStringParameter('description', 'string'));
        $fromDate = trim(Request::fromRequest()->getStringParameter('from_date', 'string'));
        $toDate = trim(Request::fromRequest()->getStringParameter('to_date', 'string'));
        $cssInsert = trim(Request::fromRequest()->getStringParameter('css_insert', 'string'));
        $customJs = trim(Request::fromRequest()->getStringParameter('js_insert', 'string'));
        $idSite = trim(Request::fromRequest()->getIntegerParameter('idSite', 0));
        $redirectUrl = $_POST['redirect_url'] . "&message=Experiment%20Created";
        $customDimension = trim(Request::fromRequest()->getStringParameter('custom_dimension', 'string'));

        $api = new API();
        $api->insertExperiment(
            $idSite,
            $name,
            $hypothesis,
            $description,
            $fromDate,
            $toDate,
            $cssInsert,
            $customJs,
            $customDimension
        );
        Url::redirectToUrl($redirectUrl);
    }

    /**
     * Delete an experiment.
     */
    public function delete()
    {
        $this->securityChecks();

        $redirectUrl = $_POST['redirect_url'];
        $id = trim(Request::fromRequest()->getIntegerParameter('id', 0));

        $api = new API();
        $api->deleteExperiment($id);
        Url::redirectToUrl($redirectUrl);
    }

    private function securityChecks()
    {
        $nonce = Common::getRequestVar('nonce', false);
        //$nonce = trim(Request::fromRequest()->getStringParameter('nonce', 'string'));

        if ($_SERVER["REQUEST_METHOD"] != "POST" || !\Piwik\Nonce::verifyNonce('SimpleABTesting.index', $nonce)) {
            echo "Not allowed. You can go to the <a href='/'>Dashboard / Home</a>.";
            exit();
        }
    }
}
