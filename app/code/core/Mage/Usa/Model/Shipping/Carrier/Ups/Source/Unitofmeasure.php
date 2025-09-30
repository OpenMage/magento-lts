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
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Unitofmeasure
{
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('usa/shipping_carrier_ups')->getCode('unit_of_measure');
        $returnArr = [];
        foreach ($unitArr as $key => $val) {
            $returnArr[] = ['value' => $key,'label' => $key];
        }
        return $returnArr;
    }
}
