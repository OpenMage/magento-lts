<?php
/**
 * Shippers Modesource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Abstract_Source_Mode
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => Mage::helper('usa')->__('Development')],
            ['value' => '1', 'label' => Mage::helper('usa')->__('Live')],
        ];
    }
}
