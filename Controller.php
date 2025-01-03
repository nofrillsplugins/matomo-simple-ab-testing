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
use Piwik\View;
use Piwik\Db;
use Piwik\Url;
use Piwik\Date;
use Piwik\Plugins\SimpleABTesting\Generator;
use Piwik\Plugins\SimpleABTesting\Helpers;

/**
 * A controller lets you for example create a page that can be added to a menu. For more information read our guide
 * http://developer.piwik.org/guides/mvc-in-piwik or have a look at the our API references for controller and view:
 * http://developer.piwik.org/api-reference/Piwik/Plugin/Controller and
 * http://developer.piwik.org/api-reference/Piwik/View
 */
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

        $name = Common::getRequestVar('name');
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        $hypothesis = Common::getRequestVar('hypothesis');
        $description = Common::getRequestVar('description');
        $fromDate = Common::getRequestVar('from_date');
        $toDate = Common::getRequestVar('to_date');
        $cssInsert = Common::getRequestVar('css_insert', '', 'string');
        $customJs = Common::getRequestVar('js_insert', '', 'string');
        $idSite = Common::getRequestVar('idSite');
        $urlRegex = Common::getRequestVar('url_regex');


        $redirectUrl = $_POST['redirect_url'] . "&message=Experiment%20Created";

        $domain = $this->getSiteDomainFromId($idSite);

        $customDimension = Common::getRequestVar('custom_dimension');

        Db::query('INSERT INTO ' . Common::prefixTable('ab_tests') . ' SET idsite = ?, domain = ?, url_regex = ?, name = ?, hypothesis = ?, description = ?, from_date = ?, to_date = ?, css_insert = ?, js_insert = ?, custom_dimension = ?', [$idSite, $domain, $urlRegex, $name, $hypothesis, $description, $fromDate, $toDate, $cssInsert, $customJs, $customDimension]);


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
        $id = Common::getRequestVar('id');

        Db::query('DELETE FROM ' . Common::prefixTable('ab_tests') . ' WHERE id = ?', [$id]);

        Url::redirectToUrl($redirectUrl);
    }

    private function securityChecks()
    {
        $nonce = Common::getRequestVar('nonce', false);

        if ($_SERVER["REQUEST_METHOD"] != "POST" || !\Piwik\Nonce::verifyNonce('SimpleABTesting.index', $nonce)) {
            echo "Not allowed. You can go to the <a href='/'>Dashboard / Home</a>.";
            exit();
        }
    }
}
