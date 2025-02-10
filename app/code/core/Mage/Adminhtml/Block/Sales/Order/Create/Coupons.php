<?php
/**
 * Adminhtml sales order create coupons block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Coupons extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_coupons_form');
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Coupons');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-promo-quote';
    }
}
