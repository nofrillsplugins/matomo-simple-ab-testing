<?php
namespace Piwik\Plugins\SimpleABTesting;

use Piwik\Menu\MenuTop;
use Piwik\Piwik;
use Piwik\Common;
use Piwik\Plugin;
use Piwik\View;
use Piwik\Db;
use Piwik\Log;

class Generator extends \Piwik\Plugin
{
    
    public function regenerateJS()
    {
        foreach (\Piwik\Site::getSites() as $site) {
            $domain = str_replace('https://', '', $site['main_url']);
            $domain = str_replace('http://', '', $domain);
            $domain = str_replace('www.', '', $domain);

            //Log::debug('pingSite: ' . $site['main_url']);
            //Log::debug('pingDomain: ' . $domain);
            // Log::debug('pingSiteId: ' . $site['idsite']);

            $experiments = \Piwik\Db::fetchAll("SELECT * FROM " . Common::prefixTable('ab_tests') . " WHERE idsite = ?", [$site['idsite']]);

            if (!empty($experiments))
            {
                $this->generateJS($domain, $experiments);
            }
            else
            {
                // at least write an empty file
                $this->createDomainJsFile($domain, "");   
            }
        }
    }

    private function generateJS($domain, $experiments)
    {
        $js = "(function() {";

        $js .= "var _paq = window._paq = window._paq || [];";

        foreach ($experiments as $experiment)
        {
            $regex = '""';
            if (!empty($experiment['url_regex']))
            {
                $regex = '/' . $experiment['url_regex'] . '/';
            }

            $js .= 'initExp(_paq, "ab_' . $experiment['name'] . '", "' . $experiment['from_date'] . 'T00:00:00Z", "' . $experiment['to_date'] . 'T23:59:00Z", ' . $regex . ', `' . Common::unsanitizeInputValues($experiment['js_insert']) . '`, `' . Common::unsanitizeInputValues($experiment['css_insert']) . '`, ' . $experiment['custom_dimension'] . ');';
        }

        $js .= $this->getJSFooterPart();

        $js .= '})();';

        $js = $this->minifyJS($js);

        $this->createDomainJsFile($domain, $js);

    }

    private function createDomainJsFile($domain, $customJs)
    {
        $publicPath = PIWIK_DOCUMENT_ROOT . '/plugins/SimpleABTesting/public/';
        $filename = $publicPath . $domain . '.js';

        // Ensure the directory exists
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        // Write the custom JS to the file
        file_put_contents($filename, $customJs);
    }

    private function getJSFooterPart()
    {
        return "
        function initExp(_paq, testName, testStartDate, testEndDate, regexString, scriptText, cssText, customDimension)
        {
            let currentVariant = getCookie(testName);
            let currentDate = new Date();
            let startDate = new Date(testStartDate);
            let endDate = new Date(testEndDate);

            /* Echoing variant
            if (typeof console !== 'undefined') {
                console.log('A/B Test', testName, 'Variant:', currentVariant);
            }
            */

            if (currentDate >= startDate && currentDate <= endDate && urlMatches(regexString)) {
                if (!currentVariant) {
                    // Randomly assign variant 0 or 1
                    currentVariant = Math.random() < 0.5 ? '0' : '1';
                    setCookie(testName, currentVariant, testEndDate);
                }

                // Check if the URL matches the regex and if the variant is 1
                if (currentVariant === '1') {
                    try {
                        // Insert CSS
                        let style = document.createElement('style');
                        style.type = 'text/css';
                        style.textContent = cssText;
                        document.head.appendChild(style);

                        // Insert JS script
                        let script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.text = scriptText;
                        document.head.appendChild(script);

                        callDimension(customDimension, testName, currentVariant);
                    } catch (e) {
                        console.error('Error in script execution', e);
                    }
                }
                else
                {
                    callDimension(customDimension, testName, currentVariant);
                }
            }
        }

        function callDimension(customDimension, testName, currentVariant)
        {
            let variantName = currentVariant === '1' ? 'variant' : 'control';

            window._paq.push(['setCustomDimension', customDimension, testName + '-' + variantName]);
        }

        // Function to set a cookie
        function setCookie(name, value, expires) {
            let date = new Date(expires);
            let cookie = name + '=' + encodeURIComponent(value) + ';expires=' + date.toUTCString() + ';path=/';
            document.cookie = cookie;
        }

        // Function to get a cookie by name
        function getCookie(name) {
            let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            if (match) {
                return decodeURIComponent(match[2]);
            }
            return null;
        }

        // Function to check if the current page URL matches the given regex
        function urlMatches(regexString) {
            try {
                let regex = new RegExp(regexString);
                return regex.test(window.location.href);
            } catch (e) {
                if (typeof console !== 'undefined') {
                    console.error('Invalid Regex:', regexString, e);
                }
                return false;
            }
        }";
    }

    private function minifyJS($js) {
        return preg_replace(
            [
                // Remove multi-line comments
                '/\/\*.*?\*\//s',
                // Remove single-line comments
                '/\/\/.*?(\n|$)/',
                // Remove whitespace (space, tab, newline) around operators and brackets
                '/\s*([\{;,\}\[\]\(\)])\s*/',
                // Reduce multiple spaces to single space
                '/\s{2,}/'
            ],
            [
                '', // Replace multi-line comments with empty string
                '', // Replace single-line comments with empty string
                '$1', // Retain operators and brackets with no surrounding spaces
                ' ' // Replace multiple spaces with a single space
            ],
            trim($js)
        );
    }
    
}
