<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
