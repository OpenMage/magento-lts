<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Pickup
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('usa/shipping_carrier_ups');
        $arr = [];
        foreach ($ups->getCode('pickup') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => Mage::helper('usa')->__($v['label'])];
        }
        return $arr;
    }
}
