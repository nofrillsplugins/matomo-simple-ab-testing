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

/**
 * This class allows you to add your own widget to the Piwik platform. In case you want to remove widgets from another
 * plugin please have a look at the "configureWidgetsList()" method.
 * To configure a widget simply call the corresponding methods as described in the API-Reference:
 * http://developer.piwik.org/api-reference/Piwik/Plugin\Widget
 */
class GetExperiments extends Widget
{
    use Helpers;

    public static function configure(WidgetConfig $config)
    {
        /**
         * Set the category the widget belongs to. You can reuse any existing widget category or define
         * your own category.
         */
        $config->setCategoryId('SimpleABTesting_SimpleABTesting');

        /**
         * Set the subcategory the widget belongs to. If a subcategory is set, the widget will be shown in the UI.
         */
        $config->setSubcategoryId('SimpleABTesting_ExistingExperiments');

        /**
         * Set the name of the widget belongs to.
         */
        $config->setName('SimpleABTesting_ExistingExperiments');

        /**
         * Set the order of the widget. The lower the number, the earlier the widget will be listed within a category.
         */
        $config->setOrder(98);
        $config->setIsEnabled(\Piwik\Piwik::isUserHasSomeAdminAccess());
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
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
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

        return $this->renderTemplate('experiments', compact('experiments', 'message', 'baseHost', 'domain', 'actionUrl', 'formattedToday', 'formattedOneMonthLater', 'currentUrl', 'refreshUrl', 'deleteUrl', 'nonce'));
    }
}
