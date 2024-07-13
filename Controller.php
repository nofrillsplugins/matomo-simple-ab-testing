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
use Piwik\Cache;
use Piwik\Date;
use Piwik\Log;
use Piwik\Site;

use Piwik\Plugins\SimpleABTesting\Generator;

/**
 * A controller lets you for example create a page that can be added to a menu. For more information read our guide
 * http://developer.piwik.org/guides/mvc-in-piwik or have a look at the our API references for controller and view:
 * http://developer.piwik.org/api-reference/Piwik/Plugin/Controller and
 * http://developer.piwik.org/api-reference/Piwik/View
 */
class Controller extends \Piwik\Plugin\Controller
{

    public function index()
    {
        echo "Go to that <a href='/'>Dashboard</a> > Tests."; exit();
    }

    /**
     * Add an experiment
     */
    public function addExperiment()
    {
        
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return $this->redirectToIndex('SimpleABTesting', 'index');
        }

        Piwik::hasUserSuperUserAccess();

        //$this->checkTokenInUrl();

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

        $redirectUrl = $_POST['redirect_url'];

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

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return $this->redirectToIndex('SimpleABTesting', 'index');
        }

        \Piwik\Piwik::checkUserHasSuperUserAccess();

        // $this->checkTokenInUrl();

        $redirectUrl = $_POST['redirect_url'];

        $generator = new Generator;
        $generator->regenerateJS();

        Url::redirectToUrl($redirectUrl);
    }

    /**
     * Refresh the JS + CSS Cache
     */
    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return $this->redirectToIndex('SimpleABTesting', 'index');
        }

        Piwik::hasUserSuperUserAccess();
        // $this->checkTokenInUrl();

        $redirectUrl = $_POST['redirect_url'];
        $id = Common::getRequestVar('id');

        Db::query('DELETE FROM ' . Common::prefixTable('ab_tests') . ' WHERE id = ?', [$id]);

        Url::redirectToUrl($redirectUrl);
    }

    /**
     * Method that gets the domain, extracted from the main url, by id
     * @param  int    $idSite 
     * @return string The domain
     */
    private function getSiteDomainFromId($idSite)
    {
        $site = new Site($idSite);
        return str_replace("www.", "", str_replace("https://", "", str_replace("http://", "", $site->getMainUrl())));
    }

}
