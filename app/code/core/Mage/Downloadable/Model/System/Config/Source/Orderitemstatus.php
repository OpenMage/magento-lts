<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Order Item Status Source
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_System_Config_Source_Orderitemstatus
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => Mage::helper('downloadable')->__('Pending'),
            ],
            [
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => Mage::helper('downloadable')->__('Invoiced'),
            ],
        ];
    }
}
