<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */

/**
 * UPS (UPS XML) mode source model
 *
 * @category   Mage
 * @package    Mage_Usa
 * @deprecated  since 1.7.0.0
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Mode
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => Mage::helper('usa')->__('Live')],
            ['value' => '0', 'label' => Mage::helper('usa')->__('Development')],
        ];
    }
}
