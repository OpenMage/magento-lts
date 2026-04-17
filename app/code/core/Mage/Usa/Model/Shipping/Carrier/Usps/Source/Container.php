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
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Container
{
    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray()
    {
        $codes = Mage::getSingleton('usa/shipping_carrier_usps')->getCode('container');
        if (!is_array($codes)) {
            return [];
        }

        $arr = [];
        foreach ($codes as $key => $value) {
            $arr[] = ['value' => $key, 'label' => $value];
        }

        return $arr;
    }
}
