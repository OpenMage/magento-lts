<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */

/**
 * Usa Ups type action Dropdown source
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Type
{
    public function toOptionArray()
    {
        return [
            ['value' => 'UPS_XML', 'label' => Mage::helper('usa')->__('United Parcel Service XML')],
            ['value' => 'UPS_REST', 'label' => Mage::helper('usa')->__('United Parcel Service REST')],
        ];
    }
}
