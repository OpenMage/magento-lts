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
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Container
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('usa/shipping_carrier_ups');
        $arr = [];
        foreach ($ups->getCode('container_description') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => Mage::helper('usa')->__($v)];
        }
        return $arr;
    }
}
