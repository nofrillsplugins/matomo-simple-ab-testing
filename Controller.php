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
use Piwik\Db;
use Piwik\Url;
use Piwik\Plugins\SimpleABTesting\Generator;
use Piwik\Plugins\SimpleABTesting\Helpers;
use Piwik\Request;
use Exception;

class Controller extends \Piwik\Plugin\Controller
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();

        if (!Piwik::hasUserSuperUserAccess()) {
            echo "Not allowed!";
            exit();
        }
    }

    public function index()
    {
        echo "Go to that <a href='/'>Home / Dashboard</a>.";
        exit();
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
        $urlRegex = trim(Request::fromRequest()->getStringParameter('url_regex', 'string'));
        $redirectUrl = $_POST['redirect_url'] . "&message=Experiment%20Created";
        $domain = $this->getSiteDomainFromId($idSite);
        $customDimension = trim(Request::fromRequest()->getStringParameter('custom_dimension', 'string'));

        $query = "INSERT INTO `" . Common::prefixTable('ab_tests') .
                 "` (idsite, domain, url_regex, name, hypothesis, description, from_date, to_date, css_insert, js_insert, custom_dimension) " .
                 "VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $params = [
            $idSite,
            $domain,
            $urlRegex,
            $name,
            $hypothesis,
            $description,
            $fromDate,
            $toDate,
            $cssInsert,
            $customJs,
            $customDimension
        ];
        try {
            $db = $this->getDb();
            $db->query($query, $params);
        } catch (Exception $e) {
            throw $e;
        }

        Url::redirectToUrl($redirectUrl);
    }

    /**
     * Refresh the JS + CSS Cache
     */
    public function refreshCache()
    {
        $this->securityChecks();

        $redirectUrl = $_POST['redirect_url'];

        $generator = new Generator();
        $generator->regenerateJS();

        Url::redirectToUrl($redirectUrl);
    }

    /**
     * Refresh the JS + CSS Cache
     */
    public function delete()
    {
        $this->securityChecks();

        $redirectUrl = $_POST['redirect_url'];
        $id = trim(Request::fromRequest()->getIntegerParameter('id', 0));

        Db::query('DELETE FROM ' . Common::prefixTable('ab_tests') . ' WHERE id = ?', [$id]);

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
