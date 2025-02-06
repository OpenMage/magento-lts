<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Method
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('usa/shipping_carrier_ups');
        $arr = [];

        // necessary after the add of Rest API
        $origins = $ups->getCode('originShipment');
        foreach ($origins as $origin) {
            foreach ($origin as $k => $v) {
                $arr[] = ['value' => $k, 'label' => Mage::helper('usa')->__($v)];
            }
        }

        // old XML API codes
        foreach ($ups->getCode('method') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => Mage::helper('usa')->__($v)];
        }

        return $arr;
    }
}
