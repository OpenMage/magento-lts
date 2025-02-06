<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
