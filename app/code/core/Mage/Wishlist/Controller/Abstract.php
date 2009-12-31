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
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist Abstract Front Controller Action
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Wishlist_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve current wishlist instance
     *
     * @return Mage_Wishlist_Model_Wishlist|false
     */
    abstract protected function _getWishlist();

    /**
     * Add all items from wishlist to shopping cart
     *
     */
    public function allcartAction()
    {
        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return ;
        }
        $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart       = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection();

        foreach ($collection as $item) {
            /* @var $item Mage_Wishlist_Model_Item */
            try {
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }

            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $e->getMessage();
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add to shopping cart');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl();
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if (!empty($notSalable)) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = Mage::helper('wishlist')->__('"%s"', $item->getProduct()->getName());
            }
            $products = join(', ', $products);

            Mage::getSingleton('wishlist/session')->addError(
                Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', $products)
            );

            $redirectUrl = $indexUrl;
        }

        if (!empty($hasOptions)) {
            if (empty($messages) && empty($notSalable) && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $products = array();
                foreach ($hasOptions as $item) {
                    $products[] = Mage::helper('wishlist')->__('"%s"', $item->getProduct()->getName());
                }
                $products = join(', ', $products);

                Mage::getSingleton('wishlist/session')->addError(
                    Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', $products)
                );

                $redirectUrl = $indexUrl;
            }
        }

        if (!empty($addedItems)) {
            $products = array();
            foreach ($addedItems as $product) {
                $products[] = Mage::helper('wishlist')->__('"%s"', $product->getName());
            }
            $products = join(', ', $products);
            $qty = count($addedItems);

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', $qty, $products)
            );
        }

        if (!empty($messages)) {
            foreach ($messages as $message) {
                Mage::getSingleton('wishlist/session')->addError($message);
            }

            $redirectUrl = $indexUrl;
        }

        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }
}
