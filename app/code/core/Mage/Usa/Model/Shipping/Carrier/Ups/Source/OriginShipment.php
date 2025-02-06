<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */

/**
 *
 * Usa Ups type action Dropdown source
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_OriginShipment
{
    public function toOptionArray()
    {
        $orShipArr = Mage::getSingleton('usa/shipping_carrier_ups')->getCode('originShipment');
        $returnArr = [];
        foreach ($orShipArr as $key => $val) {
            $returnArr[] = ['value' => $key,'label' => $key];
        }
        return $returnArr;
    }
}
