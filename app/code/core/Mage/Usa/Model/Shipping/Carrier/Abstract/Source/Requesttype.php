<?php
/**
 * Source model for Shippers Request Type
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Abstract_Source_Requesttype
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => Mage::helper('shipping')->__('Divide to equal weight (one request)')],
            ['value' => 1, 'label' => Mage::helper('shipping')->__('Use origin weight (few requests)')],
        ];
    }
}
