<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
