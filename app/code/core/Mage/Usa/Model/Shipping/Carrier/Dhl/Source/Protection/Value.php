<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Protection_Value
{
    public function toOptionArray()
    {
        $carrier = Mage::getSingleton('usa/shipping_carrier_dhl');
        $arr = [];
        foreach ($carrier->getAdditionalProtectionValueTypes() as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
