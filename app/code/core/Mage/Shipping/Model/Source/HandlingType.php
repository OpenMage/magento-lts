<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Source_HandlingType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED, 'label' => Mage::helper('shipping')->__('Fixed')],
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT, 'label' => Mage::helper('shipping')->__('Percent')],
        ];
    }
}
