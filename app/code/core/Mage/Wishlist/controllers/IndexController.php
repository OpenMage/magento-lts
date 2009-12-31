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
 * Wishlist front controller
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
                Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->_getRefererUrl());
            }
        }
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Retrieve wishlist object
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        }
        catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($this->__('Cannot create wishlist'));
            return false;
        }
        return $wishlist;
    }

    /**
     * Display customer wishlist
     */
    public function indexAction()
    {
        $this->_getWishlist();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('customer.wishlist')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $session = Mage::getSingleton('customer/session');

        /**
         *  Get referer to avoid referring to the compare popup window
         */
        if ($block && $referer = $session->getAddActionReferer(true)) {
            $block->setRefererUrl($referer);
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    /**
     * Adding new item
     */
    public function addAction()
    {
        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $this->_redirect('*/');
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product'));
            $this->_redirect('*/');
            return;
        }

        try {
            $wishlist->addNewItem($product->getId());
            Mage::dispatchEvent('wishlist_add_product', array('wishlist'=>$wishlist, 'product'=>$product));

            if ($referer = $session->getBeforeWishlistUrl()) {
                $session->setBeforeWishlistUrl(null);
            }
            else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s was successfully added to your wishlist. Click <a href="%2$s">here</a> to continue shopping', $product->getName(), $referer);
            $session->addSuccess($message);
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($this->__('There was an error while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $session->addError($this->__('There was an error while adding item to wishlist.'));
        }
        $this->_redirect('*');
    }

    /**
     * Update wishlist item comments
     */
    public function updateAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        $post = $this->getRequest()->getPost();
        if($post && isset($post['description']) && is_array($post['description'])) {
            $wishlist = $this->_getWishlist();

            foreach ($post['description'] as $itemId => $description) {
                $item = Mage::getModel('wishlist/item')->load($itemId);
                $description = (string) $description;
                if(!strlen($description) || $item->getWishlistId()!=$wishlist->getId()) {
                    continue;
                }
                try {
                    $item->setDescription($description)
                        ->save();
                }
                catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError(
                        $this->__('Can\'t save description %s', Mage::helper('core')->htmlEscape($description))
                    );
                }
            }

            if (isset($post['save_and_share'])) {
                $this->_redirect('*/*/share');
                return;
            }
        }
        $this->_redirect('*');
    }

    /**
     * Remove item
     */
    public function removeAction()
    {
        $wishlist = $this->_getWishlist();
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);

        if($item->getWishlistId()==$wishlist->getId()) {
            try {
                $item->delete();
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('There was an error while deleting item from wishlist: %s', $e->getMessage())
                );
            }
            catch(Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('There was an error while deleting item from wishlist.')
                );
            }
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     *
     */
    public function cartAction()
    {
        $wishlist   = $this->_getWishlist();
        $itemId     = (int)$this->getRequest()->getParam('item');
        /* @var $item Mage_Wishlist_Model_Item */
        $item       = Mage::getModel('wishlist/item')->load($itemId);

        if ($item->getWishlistId() == $wishlist->getId()) {
            try {
                /* @var $product Mage_Catalog_Model_Product */
                $product = Mage::getModel('catalog/product')
                    ->load($item->getProductId())
                    ->setQty(1);

                if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
                    $url = $product->getProductUrl();
                    $sep = (strpos($url, '?') !== false) ? '&' : '?';
                    $url = $url . $sep . 'options=cart';

                    $item->delete();
                    Mage::helper('wishlist')->calculate();

                    $this->_redirectUrl($url);
                    return;
                }

                Mage::getSingleton('checkout/cart')
                   ->addProduct($product)
                   ->save();

                $item->delete();
                Mage::helper('wishlist')->calculate();
            }
            catch (Exception $e) {
                if ($e instanceof Mage_Core_Exception) {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                else {
                    Mage::getSingleton('checkout/session')->addException($e,
                        Mage::helper('wishlist')->__('Can not add item to shopping cart'));
                }

                $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
                if ($url) {
                    $url = Mage::getUrl('catalog/product/view', array(
                        'id'            => $item->getProductId(),
                        'wishlist_next' => 1
                    ));
                    Mage::getSingleton('checkout/session')->setSingleWishlistId($item->getId());
                    $this->getResponse()->setRedirect($url);
                }
                else {
                    $this->_redirect('*/*/');
                }
                return;
            }
        }

        if (Mage::getStoreConfig('checkout/cart/redirect_to_cart')) {
            $this->_redirect('checkout/cart');
        }
        else {
            if ($this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
                $this->getResponse()->setRedirect(
                    Mage::helper('core')->urlDecode($this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL))
                );
            }
            else {
                $this->_redirect('*/*/');
            }
        }
    }

    /**
     * Add all items from wishlist to shopping cart
     *
     * If wishlist has products with required options
     * remove it from wishlist and redirect to product view page
     *
     */
    public function allcartAction() {
        $messages           = array();
        $urls               = array();
        $wishlistIds        = array();
        $notSalableNames    = array(); // Out of stock products message

        $wishlist   = $this->_getWishlist();
        $collection = $wishlist->getItemCollection()->load();
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart       = Mage::getSingleton('checkout/cart');

        /* @var $item Mage_Wishlist_Model_Item */
        foreach ($collection as $item) {
            try {
                /* @var $product Mage_Catalog_Model_Product */
                $product = Mage::getModel('catalog/product')
                    ->load($item->getProductId())
                    ->setQty(1);

                if ($product->isSalable()) {
                    // check required options
                    if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
                        $url = $product->getUrlModel()->getUrl($product, array('_query' => array(
                            'options' => 'cart'
                        )));

                        $item->delete();
                        Mage::helper('wishlist')->calculate();

                        $this->_redirectUrl($url);
                        return;
                    }

                    $cart->addProduct($product);
                    if (!$product->isVisibleInSiteVisibility()) {
                        $cart->getQuote()->getItemByProduct($product)
                            ->setStoreId($item->getStoreId());
                    }

                    $item->delete();
                }
                else {
                    $notSalableNames[] = $product->getName();
                }
            }
            catch (Exception $e) {
                $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
                if ($url) {
                    $url = Mage::getUrl('catalog/product/view', array(
                        'id'            => $item->getProductId(),
                        'wishlist_next' => 1
                    ));

                    $urls[]         = $url;
                    $messages[]     = $e->getMessage();
                    $wishlistIds[]  = $item->getId();
                }
                else {
                    $item->delete();
                }
            }

            Mage::getSingleton('checkout/cart')->save();
        }

        if (count($notSalableNames) > 0) {
            Mage::getSingleton('checkout/session')
                ->addNotice($this->__('This product(s) is currently out of stock:'));
            array_map(array(Mage::getSingleton('checkout/session'), 'addNotice'), $notSalableNames);
        }

        Mage::helper('wishlist')->calculate();

        if ($urls) {
            Mage::getSingleton('checkout/session')->addError(array_shift($messages));
            $this->getResponse()->setRedirect(array_shift($urls));

            Mage::getSingleton('checkout/session')->setWishlistPendingUrls($urls);
            Mage::getSingleton('checkout/session')->setWishlistPendingMessages($messages);
            Mage::getSingleton('checkout/session')->setWishlistIds($wishlistIds);
        }
        else {
            $this->_redirect('checkout/cart');
        }
    }

    public function shareAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('wishlist/session');
        $this->renderLayout();
    }

    public function sendAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $emails = explode(',', $this->getRequest()->getPost('emails'));
        $message= nl2br(htmlspecialchars((string) $this->getRequest()->getPost('message')));
        $error  = false;
        if (empty($emails)) {
            $error = $this->__('Email address can\'t be empty.');
        }
        else {
            foreach ($emails as $index => $email) {
                $email = trim($email);
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $error = $this->__('You input not valid email address.');
                    break;
                }
                $emails[$index] = $email;
            }
        }
        if ($error) {
            Mage::getSingleton('wishlist/session')->addError($error);
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        try {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $wishlist = $this->_getWishlist();

            /*if share rss added rss feed to email template*/
            if ($this->getRequest()->getParam('rss_url')) {
                $rss_url = $this->getLayout()->createBlock('wishlist/share_email_rss')->toHtml();
                $message .=$rss_url;
            }
            $wishlistBlock = $this->getLayout()->createBlock('wishlist/share_email_items')->toHtml();

            $emails = array_unique($emails);
            /* @var $emailModel Mage_Core_Model_Email_Template */
            $emailModel = Mage::getModel('core/email_template');

            foreach($emails as $email) {
                $emailModel->sendTransactional(
                    Mage::getStoreConfig('wishlist/email/email_template'),
                    Mage::getStoreConfig('wishlist/email/email_identity'),
                    $email,
                    null,
                    array(
                        'customer'      => $customer,
                        'salable'       => $wishlist->isSalable() ? 'yes' : '',
                        'items'         => $wishlistBlock,
                        'addAllLink'    => Mage::getUrl('*/shared/allcart', array('code' => $wishlist->getSharingCode())),
                        'viewOnSiteLink'=> Mage::getUrl('*/shared/index', array('code' => $wishlist->getSharingCode())),
                        'message'       => $message
                    ));
            }

            $wishlist->setShared(1);
            $wishlist->save();

            $translate->setTranslateInline(true);

            Mage::dispatchEvent('wishlist_share', array('wishlist'=>$wishlist));
            Mage::getSingleton('customer/session')->addSuccess(
                $this->__('Your Wishlist was successfully shared')
            );
            $this->_redirect('*/*');
        }
        catch (Exception $e) {
            $translate->setTranslateInline(true);

            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
        }
    }
}
