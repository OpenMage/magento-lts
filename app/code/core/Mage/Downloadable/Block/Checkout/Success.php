<?php
/**
 * Downloadable checkout success page
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{
    /**
     * Return true if order(s) has one or more downloadable products
     *
     * @return bool
     */
    public function getOrderHasDownloadable()
    {
        $hasDownloadableFlag = Mage::getSingleton('checkout/session')
            ->getHasDownloadableProducts(true);
        if (!$this->isOrderVisible()) {
            return false;
        }
        /**
         * if use guest checkout
         */
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            return false;
        }
        return $hasDownloadableFlag;
    }

    /**
     * Return url to list of ordered downloadable products of customer
     *
     * @return string
     */
    public function getDownloadableProductsUrl()
    {
        return $this->getUrl('downloadable/customer/products', ['_secure' => true]);
    }
}
