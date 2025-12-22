<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart api
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Coupon_Api extends Mage_Checkout_Model_Api_Resource
{
    /**
     * @param  int        $quoteId
     * @param  string     $couponCode
     * @param  int|string $store
     * @return bool
     */
    public function add($quoteId, $couponCode, $store = null)
    {
        return $this->_applyCoupon($quoteId, $couponCode, $store = null);
    }

    /**
     * @param  int        $quoteId
     * @param  int|string $store
     * @return bool
     */
    public function remove($quoteId, $store = null)
    {
        $couponCode = '';
        return $this->_applyCoupon($quoteId, $couponCode, $store);
    }

    /**
     * @param  int        $quoteId
     * @param  int|string $store
     * @return string
     */
    public function get($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        return $quote->getCouponCode();
    }

    /**
     * @param  int        $quoteId
     * @param  string     $couponCode
     * @param  int|string $store
     * @return bool
     */
    protected function _applyCoupon($quoteId, $couponCode, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        if (!$quote->getItemsCount()) {
            $this->_fault('quote_is_empty');
        }

        $oldCouponCode = $quote->getCouponCode();
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return false;
        }

        try {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();
        } catch (Exception $exception) {
            $this->_fault('cannot_apply_coupon_code', $exception->getMessage());
        }

        if ($couponCode) {
            if (!$couponCode == $quote->getCouponCode()) {
                $this->_fault('coupon_code_is_not_valid');
            }
        }

        return true;
    }
}
