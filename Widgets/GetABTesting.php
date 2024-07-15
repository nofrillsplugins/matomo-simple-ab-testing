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
use Piwik\Site;
use Piwik\Plugins\SimpleABTesting\Helpers;

/**
 * This class allows you to add your own widget to the Piwik platform. In case you want to remove widgets from another
 * plugin please have a look at the "configureWidgetsList()" method.
 * To configure a widget simply call the corresponding methods as described in the API-Reference:
 * http://developer.piwik.org/api-reference/Piwik/Plugin\Widget
 */
class GetABTesting extends Widget
{
    use Helpers;

    public static function configure(WidgetConfig $config)
    {
        /**
         * Set the category the widget belongs to. You can reuse any existing widget category or define
         * your own category.
         */
        $config->setCategoryId('SimpleABTesting_Tests');

        /**
         * Set the subcategory the widget belongs to. If a subcategory is set, the widget will be shown in the UI.
         */
        $config->setSubcategoryId('General_Overview');

        /**
         * Set the name of the widget belongs to.
         */
        $config->setName('SimpleABTesting_ABTesting');

        /**
         * Set the order of the widget. The lower the number, the earlier the widget will be listed within a category.
         */
        $config->setOrder(99);

        /**
         * Optionally set URL parameters that will be used when this widget is requested.
         * $config->setParameters(array('myparam' => 'myvalue'));
         */

        $config->setIsEnabled(\Piwik\Piwik::hasUserSuperUserAccess());

        /**
         * Define whether a widget is enabled or not. For instance some widgets might not be available to every user or
         * might depend on a setting (such as Ecommerce) of a site. In such a case you can perform any checks and then
         * set `true` or `false`. If your widget is only available to users having super user access you can do the
         * following:
         *
         * $config->setIsEnabled(\Piwik\Piwik::hasUserSuperUserAccess());
         * or
         * if (!\Piwik\Piwik::hasUserSuperUserAccess())
         *     $config->disable();
         */
    }

    /**
     * This method renders the widget. It's on you how to generate the content of the widget.
     * As long as you return a string everything is fine. You can use for instance a "Piwik\View" to render a
     * twig template. In such a case don't forget to create a twig template (eg. myViewTemplate.twig) in the
     * "templates" directory of your plugin.
     *
     * @return string
     */
    public function render()
    {
        $idSite = Common::getRequestVar('idSite');

        $exps = Db::fetchAll("SELECT * FROM " . Common::prefixTable('ab_tests') . " WHERE idsite = ?", [$idSite]);

        $experiments = [];
        foreach ($exps as $n => $exp)
        {
            $experiments[$n] = $exp;
            $experiments[$n]['css_insert'] = Common::unsanitizeInputValues($exp['css_insert']);
            $experiments[$n]['js_insert'] = Common::unsanitizeInputValues($exp['js_insert']);

            if (!empty($exp['custom_dimension']))
            {
                $customDimensionsUrl = $this->getCustomUrl('range', $exp['from_date']  .',' . $exp['to_date'], 'General_Visitors', 'customdimension' . $exp['custom_dimension']);
                $experiments[$n]['report_url'] = $customDimensionsUrl;
            }
            
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
        $customDimensionsUrl = $this->getCustomDimensionsUrl();

        $message = Common::getRequestVar('message', '', 'string');

        $nonce = \Piwik\Nonce::getNonce('SimpleABTesting.index');

        $domain = $this->getSiteDomainFromId($idSite);

        return $this->renderTemplate('index', compact('experiments', 'message', 'baseHost', 'domain', 'actionUrl', 'formattedToday', 'formattedOneMonthLater', 'currentUrl', 'refreshUrl', 'deleteUrl', 'customDimensionsUrl', 'nonce'));
    }

    // http://matomo.test/index.php?module=CoreHome&action=index&idSite=1&period=day&date=yesterday#?idSite=1&period=day&date=yesterday&category=SimpleABTesting_Tests&subcategory=General_Overview

    // period=range&date=2024-07-08,2024-07-12&idSite=1&category=General_Visitors&subcategory=customdimension1

}
