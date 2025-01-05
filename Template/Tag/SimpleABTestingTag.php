<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SimpleABTesting\Template\Tag;

use Piwik\Piwik;
use Piwik\Settings\FieldConfig;
use Piwik\Plugins\TagManager\Template\Tag\BaseTag;
use Piwik\Db;
use Piwik\Common;

class SimpleABTestingTag extends BaseTag
{
    public function getName()
    {
        return "Simple A/B Testing";
    }

    public function getIcon()
    {
        return 'plugins/SimpleABTesting/assets/simple-ab-testing.svg';
    }

    public function getParameters()
    {


        return array(
            $this->makeSetting('experiment', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = Piwik::translate('SimpleABTesting_TagChooseExperiment');
                ;
                $field->availableValues = $this->getExperiments();
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->description = Piwik::translate('SimpleABTesting_SimpleABTestingTagDescription');
                ;
            }),
        );
    }

    public function getCategory()
    {
        return self::CATEGORY_DEVELOPERS;
    }

    private function getExperiments()
    {
        $sql = "SELECT id, name, from_date, to_date, css_insert, js_insert, custom_dimension FROM " . Common::prefixTable('simple_ab_testing_experiments');
        $result = Db::fetchAll($sql);

        $options = []; // Initialize the array to store experiment data
        foreach ($result as $experiment) {
            $cssInsert = urlencode($experiment['css_insert']);
            $jsInsert = urlencode($experiment['js_insert']);

            // Create a string of concatenated values for the experiment
            $values = $experiment['name'] . ','
                . $experiment['from_date'] . ','
                . $experiment['to_date'] . ','
                . $cssInsert . ','
                . $jsInsert . ','
                . $experiment['custom_dimension'];

            // Use the experiment name as the key and values as the value
            $options[$values] = $experiment['name'];
        }
        return $options;
    }
}
