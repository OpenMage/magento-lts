<?php
/**
 * Google Analytics system config source type
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Model_System_Config_Source_Type
{
    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_GoogleAnalytics_Helper_Data::TYPE_ANALYTICS4,
                'label' => Mage::helper('googleanalytics')->__('Google Analytics 4'),
            ],
        ];
    }
}
