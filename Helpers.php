<?php

namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Common;
use Piwik\Url;
use Piwik\Site;
use Piwik\Db;

trait Helpers
{
    private function getCustomUrl($period = null, $date = null, $category = null, $subcategory = null)
    {
        $baseUrl = Url::getCurrentUrlWithoutFileName();
        $idSite = Common::getRequestVar('idSite', 1, 'int');

        $period = $period ?? Common::getRequestVar('period');
        $date = $date ?? Common::getRequestVar('date');

        $category = $category ?? 'SimpleABTesting_SimpleABTesting';
        $subcategory = $subcategory ?? 'General_Overview';

        $params = [
            'module' => 'CoreHome',
            'action' => 'index',
            'idSite' => $idSite,
            'period' => $period,
            'date' => $date,
        ];

        return $baseUrl . 'index.php?' . http_build_query($params) . '#' . http_build_query($params) .
               '&category=' . $category . '&subcategory=' . $subcategory;
    }

    private function getCustomDimensionsUrl()
    {
        $baseUrl = Url::getCurrentUrlWithoutFileName();
        $idSite = Common::getRequestVar('idSite', 1, 'int');

        $params = [
            'module' => 'CustomDimensions',
            'action' => 'manage',
            'idSite' => $idSite,
            'period' => 'day',
            'date' => 'today'
        ];

        return $baseUrl . 'index.php?' . http_build_query($params);
    }

    private function getHost($url)
    {
        $url = str_replace("https://", "", $url);
        $url = str_replace("http://", "", $url);

        return $url;
    }

    private function getDomain($url)
    {
        $url = $this->getHost($url);
        $url = str_replace("www.", "", $url);

        return $url;
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

    private function getDb()
    {
        return Db::get();
    }
}
