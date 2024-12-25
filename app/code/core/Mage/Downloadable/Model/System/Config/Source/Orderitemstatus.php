<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
