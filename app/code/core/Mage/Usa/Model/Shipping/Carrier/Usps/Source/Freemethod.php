<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Freemethod extends Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method
{
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, ['value' => '', 'label' => Mage::helper('shipping')->__('None')]);
        return $arr;
    }
}
