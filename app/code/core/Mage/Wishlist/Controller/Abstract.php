<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist Abstract Front Controller Action
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
abstract class Mage_Wishlist_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Filter to convert localized values to internal ones
     * @var Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter = null;

    /**
     * Is need check a Formkey
     * @var bool
     */
    protected $_isCheckFormKey = true;

    /**
     * Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     * @deprecated
     */
    protected function _processLocalizedQty($qty)
    {
        $qty = (float) $qty;
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }

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
        if ($this->_isCheckFormKey && !$this->_validateFormKey()) {
            $this->_forward('noRoute');
            return;
        }

        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return;
        }
        $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages   = [];
        $addedItems = [];
        $notSalable = [];
        $hasOptions = [];

        $cart       = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

        $qtysString = $this->getRequest()->getParam('qty');
        if (isset($qtysString)) {
            $qtys = array_filter(json_decode($qtysString), fn($tmpString) => strlen($tmpString ?? ''));
        }

        /** @var Mage_Wishlist_Model_Item $item */
        foreach ($collection as $item) {
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } elseif ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }

                $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                if ($cartItem) {
                    $cart->getQuote()->deleteItem($cartItem);
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', ['code' => $wishlist->getSharingCode()]);
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } elseif ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = [];
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', implode(', ', $products));
        }

        if ($hasOptions) {
            $products = [];
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', implode(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            } catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }

            $products = [];
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), implode(', ', $products)),
            );

            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }
}
