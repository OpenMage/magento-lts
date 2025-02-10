<?php
/**
 * Fedex dropoff source implementation
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Dropoff
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('usa/shipping_carrier_fedex');
        $arr = [];
        foreach ($fedex->getCode('dropoff') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
