<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 *
 * Usa Ups type action Dropdown source
 *
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
