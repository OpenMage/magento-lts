<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    /**
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            $title = Mage::helper('sales')->__('Discount');
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $title .= ' (' . $couponCode . ')';
            }
            $address->addTotal([
                'code' => 'discount',
                'title' => $title,
                'value' => -$amount,
            ]);
        }
        return $this;
    }
}
