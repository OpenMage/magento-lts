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
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist shared items controllers
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_SharedController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $code = (string) $this->getRequest()->getParam('code');
        if (empty($code)) {
            $this->_forward('noRoute');
            return;
        }
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($code);

        if ($wishlist->getCustomerId() && $wishlist->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId()) {
            $this->_redirectUrl(Mage::helper('wishlist')->getListUrl());
            return;
        }

        if(!$wishlist->getId()) {
            $this->_forward('noRoute');
            return;
        } else {
            Mage::register('shared_wishlist', $wishlist);
            $this->loadLayout();
            $this->_initLayoutMessages('wishlist/session');
            $this->renderLayout();
        }

    }

    public function allcartAction()
    {
        $code = (string) $this->getRequest()->getParam('code');
        if (empty($code)) {
            $this->_forward('noRoute');
            return;
        }

        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCode($code);
        Mage::getSingleton('checkout/session')->setSharedWishlist($code);

        if (!$wishlist->getId()) {
            $this->_forward('noRoute');
            return;
        } else {
            $urls = false;
            foreach ($wishlist->getProductCollection() as $item) {
                try {
                    $product = Mage::getModel('catalog/product')
                        ->load($item->getProductId());
                    if ($product->isSalable()){
                        Mage::getSingleton('checkout/cart')->addProduct($product);
                    }
                }
                catch (Exception $e) {
                    $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
                    if ($url){
                        $url = Mage::getModel('core/url')->getUrl('catalog/product/view', array(
                            'id'=>$item->getProductId(),
                            'wishlist_next'=>1
                        ));

                        $urls[] = $url;
                        $messages[] = $e->getMessage();
                        $wishlistIds[] = $item->getId();
                    }
                }

                Mage::getSingleton('checkout/cart')->save();
            }
            if ($urls) {
                Mage::getSingleton('checkout/session')->addError(array_shift($messages));
                $this->getResponse()->setRedirect(array_shift($urls));

                Mage::getSingleton('checkout/session')->setWishlistPendingUrls($urls);
                Mage::getSingleton('checkout/session')->setWishlistPendingMessages($messages);
                Mage::getSingleton('checkout/session')->setWishlistIds($wishlistIds);
            } else {
                $this->_redirect('checkout/cart');
            }
        }
    }
}