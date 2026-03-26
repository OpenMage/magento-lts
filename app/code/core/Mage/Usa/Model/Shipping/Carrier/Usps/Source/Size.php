<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Size
{
    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray()
    {
        $codes = Mage::getSingleton('usa/shipping_carrier_usps')->getCode('size');
        if (!is_array($codes)) {
            return [];
        }
        $arr = [];
        foreach ($codes as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }

        return $arr;
    }
}
