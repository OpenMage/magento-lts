<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout default helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GUEST_CHECKOUT = 'checkout/options/guest_checkout';
    const XML_PATH_CUSTOMER_MUST_BE_LOGGED = 'checkout/options/customer_must_be_logged';

    protected $_agreements = null;

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    public function convertPrice($price, $format=true)
    {
        return $this->getQuote()->getStore()->convertPrice($price, $format);
    }

    public function getRequiredAgreementIds()
    {
        if (is_null($this->_agreements)) {
            if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $this->_agreements = array();
            } else {
                $this->_agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->getAllIds();
            }
        }
        return $this->_agreements;
    }

    /**
     * Get onepage checkout availability
     *
     * @return bool
     */
    public function canOnepageCheckout()
    {
        return (bool)Mage::getStoreConfig('checkout/options/onepage_checkout_enabled');
    }

    /**
     * Get sales item (quote item, order item etc) price including tax based on row total and tax amount
     * excluding weee tax
     *
     * @param   Varien_Object $item
     * @return  float
     */
    public function getPriceInclTax($item)
    {
        if ($item->getPriceInclTax()) {
            return $item->getPriceInclTax();
        }
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));

        //Unit price is rowtotal/qty
        return $qty > 0 ? $this->getSubtotalInclTax($item)/$qty :0;
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax
     *
     * @param   Varien_Object $item
     * @return  float
     */
    public function getSubtotalInclTax($item)
    {
        if ($item->getRowTotalInclTax()) {
            return $item->getRowTotalInclTax();
        }
        //Since tax amount contains weee tax
        $tax = $item->getTaxAmount() + $item->getDiscountTaxCompensation()
            - $this->_getWeeeHelper()->getTotalRowTaxAppliedForWeeeTax($item);;

        return $item->getRowTotal() + $tax;
    }

    /**
     * Returns the helper for weee
     *
     * @return Mage_Weee_Helper_Data
     */
    protected function _getWeeeHelper()
    {
        return Mage::helper('weee');
    }

    /**
     * Get the base price of the item including tax , excluding weee
     *
     * @param Varien_Object $item
     * @return float
     */
    public function getBasePriceInclTax($item)
    {
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));

        return $qty > 0 ? $this->getBaseSubtotalInclTax($item) / $qty : 0;
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax excluding wee
     *
     * @param Varien_Object $item
     * @return float
     */
    public function getBaseSubtotalInclTax($item)
    {
        $tax = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation()
            - $this->_getWeeeHelper()->getBaseTotalRowTaxAppliedForWeeeTax($item);
        return $item->getBaseRowTotal()+$tax;
    }

    /**
     * Send email id payment was failed
     *
     * @param Mage_Sales_Model_Quote $checkout
     * @param string $message
     * @param string $checkoutType
     * @return Mage_Checkout_Helper_Data
     */
    public function sendPaymentFailedEmail($checkout, $message, $checkoutType = 'onepage')
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */

        $template = Mage::getStoreConfig('checkout/payment_failed/template', $checkout->getStoreId());

        $copyTo = $this->_getEmails('checkout/payment_failed/copy_to', $checkout->getStoreId());
        $copyMethod = Mage::getStoreConfig('checkout/payment_failed/copy_method', $checkout->getStoreId());
        if ($copyTo && $copyMethod == 'bcc') {
            $mailTemplate->addBcc($copyTo);
        }

        $_reciever = Mage::getStoreConfig('checkout/payment_failed/reciever', $checkout->getStoreId());
        $sendTo = array(
            array(
                'email' => Mage::getStoreConfig('trans_email/ident_'.$_reciever.'/email', $checkout->getStoreId()),
                'name'  => Mage::getStoreConfig('trans_email/ident_'.$_reciever.'/name', $checkout->getStoreId())
            )
        );

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'email' => $email,
                    'name'  => null
                );
            }
        }
        $shippingMethod = '';
        if ($shippingInfo = $checkout->getShippingAddress()->getShippingMethod()) {
            $data = explode('_', $shippingInfo);
            $shippingMethod = $data[0];
        }

        $paymentMethod = '';
        if ($paymentInfo = $checkout->getPayment()) {
            $paymentMethod = $paymentInfo->getMethod();
        }

        $items = '';
        foreach ($checkout->getAllVisibleItems() as $_item) {
            /* @var $_item Mage_Sales_Model_Quote_Item */
            $items .= $_item->getProduct()->getName() . '  x '. $_item->getQty() . '  '
                . $checkout->getStoreCurrencyCode() . ' '
                . $_item->getProduct()->getFinalPrice($_item->getQty()) . "\n";
        }
        $total = $checkout->getStoreCurrencyCode() . ' ' . $checkout->getGrandTotal();

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$checkout->getStoreId()))
                ->sendTransactional(
                $template,
                Mage::getStoreConfig('checkout/payment_failed/identity', $checkout->getStoreId()),
                $recipient['email'],
                $recipient['name'],
                    array(
                        'reason'          => $message,
                        'checkoutType'    => $checkoutType,
                        'dateAndTime'     => Mage::app()->getLocale()->date(),
                        'customer'        => Mage::helper('customer')->getFullCustomerName($checkout),
                        'customerEmail'   => $checkout->getCustomerEmail(),
                        'billingAddress'  => $checkout->getBillingAddress(),
                        'shippingAddress' => $checkout->getShippingAddress(),
                        'shippingMethod'  => Mage::getStoreConfig('carriers/' . $shippingMethod . '/title'),
                        'paymentMethod'   => Mage::getStoreConfig('payment/' . $paymentMethod . '/title'),
                        'items'           => nl2br($items),
                        'total'           => $total,
                    )
            );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    protected function _getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned.
     *
     * @return bool
     */
    public function isMultishippingCheckoutAvailable()
    {
        $quote = $this->getQuote();
        $isMultiShipping = (bool)(int)Mage::getStoreConfig('shipping/option/checkout_multiple');
        if ((!$quote) || !$quote->hasItems()) {
            return $isMultiShipping;
        }
        $maximunQty = (int)Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');
        return $isMultiShipping
            && !$quote->hasItemsWithDecimalQty()
            && $quote->validateMinimumAmount(true)
            && (($quote->getItemsSummaryQty() - $quote->getItemVirtualQty()) > 0)
            && ($quote->getItemsSummaryQty() <= $maximunQty)
            && !$quote->hasNominalItems()
            ;
    }

    /**
     * Check is allowed Guest Checkout
     * Use config settings and observer
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isAllowedGuestCheckout(Mage_Sales_Model_Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }
        $guestCheckout = Mage::getStoreConfigFlag(self::XML_PATH_GUEST_CHECKOUT, $store);

        if ($guestCheckout == true) {
            $result = new Varien_Object();
            $result->setIsAllowed($guestCheckout);
            Mage::dispatchEvent('checkout_allow_guest', array(
                'quote'  => $quote,
                'store'  => $store,
                'result' => $result
            ));

            $guestCheckout = $result->getIsAllowed();
        }

        return $guestCheckout;
    }

    /**
     * Check if context is checkout
     *
     * @return bool
     */
    public function isContextCheckout()
    {
        return (Mage::app()->getRequest()->getParam('context') == 'checkout');
    }

    /**
     * Check if user must be logged during checkout process
     *
     * @return boolean
     */
    public function isCustomerMustBeLogged()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_MUST_BE_LOGGED);
    }
}
