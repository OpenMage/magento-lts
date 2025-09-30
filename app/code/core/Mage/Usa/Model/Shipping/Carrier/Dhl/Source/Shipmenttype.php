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
class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Shipmenttype
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('usa/shipping_carrier_dhl');
        $arr = [];
        foreach ($fedex->getCode('shipment_type') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
