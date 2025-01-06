<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting\Widgets;

use Piwik\Widget\Widget;
use Piwik\Widget\WidgetConfig;
use Piwik\Db;
use Piwik\Url;
use Piwik\Common;
use Piwik\Plugins\SimpleABTesting\Helpers;
use Piwik\Request;

class GetABTesting extends Widget
{
    use Helpers;

    public static function configure(WidgetConfig $config)
    {
        $config->setCategoryId('SimpleABTesting_SimpleABTesting');
        $config->setSubcategoryId('SimpleABTesting_CreateNewExperiment');
        $config->setName('SimpleABTesting_CreateNewExperiment');
        $config->setOrder(90);
        $config->setIsEnabled(\Piwik\Piwik::isUserHasSomeAdminAccess());
    }

    /**
     * @return string
     */
    public function render($idSite = null)
    {
        if (!isset($idSite)) {
            $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
        }

        $exps = Db::fetchAll("SELECT * FROM " . Common::prefixTable('simple_ab_testing_experiments') . " WHERE idsite = ?", [$idSite]);

        $experiments = [];
        foreach ($exps as $n => $exp) {
            $experiments[$n] = $exp;
            $experiments[$n]['css_insert'] = Common::unsanitizeInputValues($exp['css_insert']);
            $experiments[$n]['js_insert'] = Common::unsanitizeInputValues($exp['js_insert']);
        }

        $currentUrl = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $actionUrl = Url::getCurrentQueryStringWithParametersModified([
            'module' => 'SimpleABTesting',
            'action' => 'addExperiment'
        ]);

        $refreshUrl = Url::getCurrentQueryStringWithParametersModified([
            'module' => 'SimpleABTesting',
            'action' => 'refreshCache'
        ]);

        $deleteUrl = Url::getCurrentQueryStringWithParametersModified([
            'module' => 'SimpleABTesting',
            'action' => 'delete'
        ]);

        $today = new \DateTime();
        $oneMonthLater = (clone $today)->modify('+1 month');

        $formattedToday = $today->format('Y-m-d');
        $formattedOneMonthLater = $oneMonthLater->format('Y-m-d');

        $baseUrl = Url::getCurrentUrlWithoutFileName();
        $baseHost = $this->getHost($baseUrl);

        $currentUrl = $this->getCustomUrl();

        $message = trim(Request::fromRequest()->getStringParameter('message', ''));

        $nonce = \Piwik\Nonce::getNonce('SimpleABTesting.index');

        $domain = $this->getSiteDomainFromId($idSite);

        return $this->renderTemplate('index', compact('experiments', 'message', 'baseHost', 'domain', 'actionUrl', 'formattedToday', 'formattedOneMonthLater', 'currentUrl', 'refreshUrl', 'deleteUrl', 'nonce'));
    }
}
