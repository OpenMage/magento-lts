<?php
/**
 * Quote address attribute frontend discount resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Discount extends Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
{
    /**
     * Fetch discount
     *
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            $title = Mage::helper('sales')->__('Discount');
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $title .= sprintf(' (%s)', $couponCode);
            }
            $address->addTotal([
                'code'  => 'discount',
                'title' => $title,
                'value' => -$amount,
            ]);
        }
        return $this;
    }
}
