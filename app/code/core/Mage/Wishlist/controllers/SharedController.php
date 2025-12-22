<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist shared items controllers
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_SharedController extends Mage_Wishlist_Controller_Abstract
{
    /**
     * Is need check a Formkey
     * @var bool
     */
    protected $_isCheckFormKey = false;

    /**
     * Retrieve wishlist instance by requested code
     *
     * @return false|Mage_Wishlist_Model_Wishlist
     * @throws Mage_Core_Exception
     */
    protected function _getWishlist()
    {
        $code     = (string) $this->getRequest()->getParam('code');
        if (empty($code)) {
            return false;
        }

        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($code);
        if (!$wishlist->getId()) {
            return false;
        }

        Mage::getSingleton('checkout/session')->setSharedWishlist($code);

        return $wishlist;
    }

    /**
     * Shared wishlist view page
     *
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function indexAction()
    {
        $wishlist   = $this->_getWishlist();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        if ($wishlist && $wishlist->getCustomerId() && $wishlist->getCustomerId() == $customerId) {
            $this->_redirectUrl(Mage::helper('wishlist')->getListUrl($wishlist->getId()));
            return;
        }

        Mage::register('shared_wishlist', $wishlist);

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('wishlist/session');
        $this->renderLayout();
    }

    /**
     * Add shared wishlist item to shopping cart
     *
     * If Product has required options - redirect
     * to product view page with message about needed defined required options
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function cartAction()
    {
        $itemId = (int) $this->getRequest()->getParam('item');
        $code = $this->getRequest()->getParam('code');

        /** @var Mage_Wishlist_Model_Item $item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($code);
        $redirectUrl = Mage::getUrl('*/*/index', ['code' => $code]);

        /** @var Mage_Wishlist_Model_Session $session */
        $session    = Mage::getSingleton('wishlist/session');
        $cart       = Mage::getSingleton('checkout/cart');

        try {
            $options = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter([$itemId]);
            $item->setOptions($options->getOptionsByItem($itemId));

            $item->addToCart($cart);
            $cart->save()->getQuote()->collectTotals();

            if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
                $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            if ($mageCoreException->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
            } elseif ($mageCoreException->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SPECIFIED_PRODUCT) {
                if (!$wishlist->getItemsCount()) {
                    $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
                    $session = Mage::getSingleton('catalog/session');
                }

                $message = Mage::helper('wishlist')->__('Cannot add the selected product to shopping cart because the product was removed from the wishlist');
                $session->addNotice($message);
            } else {
                Mage::getSingleton('catalog/session')->addNotice($mageCoreException->getMessage());
                $redirectUrl = $item->getProductUrl();
            }
        } catch (Exception $exception) {
            $session->addException($exception, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
        }

        return $this->_redirectUrl($redirectUrl);
    }
}
