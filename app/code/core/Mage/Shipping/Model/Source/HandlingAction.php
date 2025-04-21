<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Source_HandlingAction
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERORDER, 'label' => Mage::helper('shipping')->__('Per Order')],
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERPACKAGE , 'label' => Mage::helper('shipping')->__('Per Package')],
        ];
    }
}
