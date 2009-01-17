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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_GoogleCheckout_RedirectController extends Mage_Core_Controller_Front_Action
{
    /**
     *  Send request to Google Checkout and return Responce Api
     *
     *  @param    none
     *  @return	  object Mage_GoogleCheckout_Model_Api_Xml_Checkout
     */
    protected function _getApi ()
    {
        $session = Mage::getSingleton('checkout/session');

        $api = Mage::getModel('googlecheckout/api');

        if (!$session->getQuote()->hasItems()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
            $api->setError(true);
        }

        $storeQuote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());
        $storeQuote->merge($session->getQuote());
        $storeQuote
            ->setItemsCount($session->getQuote()->getItemsCount())
            ->setItemsQty($session->getQuote()->getItemsQty())
            ->setChangedFlag(false);
        $storeQuote->save();

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();
        $session->getQuote()
            ->setForcedCurrency($currency)
            ->collectTotals()
            ->save();

        if (!$api->getError()) {
            $api = $api->setAnalyticsData($this->getRequest()->getPost('analyticsdata'))
                ->checkout($session->getQuote());

            $response = $api->getResponse();
            if ($api->getError()) {
                Mage::getSingleton('checkout/session')->addError($api->getError());
            } else {
                $session->replaceQuote($storeQuote);
                Mage::getModel('checkout/cart')->init()->save();
                if (Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
                    $session->setGoogleCheckoutQuoteId($session->getQuoteId());
                    $session->setQuoteId(null);
                }
            }
        }
        return $api;
    }

    public function checkoutAction()
    {
        $api = $this->_getApi();

        if ($api->getError()) {
            $url = Mage::getUrl('checkout/cart');
        } else {
            $url = $api->getRedirectUrl();
        }
        $this->getResponse()->setRedirect($url);
    }

    /**
     * When a customer chooses Google Checkout on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $api = $this->_getApi();

        if ($api->getError()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
            return;
        } else {
            $url = $api->getRedirectUrl();
            $this->loadLayout();
            $this->getLayout()->getBlock('googlecheckout_redirect')->setRedirectUrl($url);
            $this->renderLayout();
        }
    }

    public function cartAction()
    {
        if (Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
            $session = Mage::getSingleton('checkout/session');
            if ($session->getQuoteId()) {
                $session->getQuote()->delete();
            }
            $session->setQuoteId($session->getGoogleCheckoutQuoteId());
            $session->setGoogleCheckoutQuoteId(null);
        }

        $this->_redirect('checkout/cart');
    }

    public function continueAction()
    {
        $session = Mage::getSingleton('checkout/session');

        if ($quoteId = $session->getGoogleCheckoutQuoteId()) {
            $quote = Mage::getModel('sales/quote')->load($quoteId)
                ->setIsActive(false)->save();
            $session->unsQuoteId();
        }

//        if (Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
//            $session->unsGoogleCheckoutQuoteId();
//        }

        $url = Mage::getStoreConfig('google/checkout/continue_shopping_url');
        if (empty($url)) {
            $this->_redirect('');
        } elseif (substr($url, 0, 4)==='http') {
            $this->getResponse()->setRedirect($url);
        } else {
            $this->_redirect($url);
        }
    }
}