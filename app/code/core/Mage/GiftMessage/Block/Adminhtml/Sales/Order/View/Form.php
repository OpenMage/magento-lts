<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view gift message form
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        $order = Mage::registry('current_order');
        if ($order) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getGiftMessageId()) {
                    return true;
                }
            }
        }
        return false;
    }
}
