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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders creation process controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve gift message save model
     *
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return Mage::getSingleton('adminhtml/giftmessage_save');
    }

    /**
     * Initialize order creation session data
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession()
    {
        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }

        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int) $storeId);
        }

        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string) $currencyId);
            $this->_getOrderCreateModel()->setRecollect(true);
        }
        return $this;
    }

    /**
     * Processing request data
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _processData()
    {
        /**
         * Saving order data
         */
        if ($data = $this->getRequest()->getPost('order')) {
            $this->_getOrderCreateModel()->importPostData($data);
        }

        /**
         * Flag for using billing address for shipping
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual()) {
            $syncFlag = $this->getRequest()->getPost('shipping_as_billing');
            if (!is_null($syncFlag)) {
                $this->_getOrderCreateModel()->setShippingAsBilling((int)$syncFlag);
            }
        }

        /**
         * Change shipping address flag
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')) {
            $this->_getOrderCreateModel()->resetShippingMethod(true);
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->_getOrderCreateModel()->getQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')) {
            $this->_getOrderCreateModel()->collectShippingRates();
        }


        /**
         * Apply mass changes from sidebar
         */
        if ($data = $this->getRequest()->getPost('sidebar')) {
            $this->_getOrderCreateModel()->applySidebarData($data);
        }

        /**
         * Adding product to quote from shoping cart, wishlist etc.
         */
        if ($productId = (int) $this->getRequest()->getPost('add_product')) {
            $this->_getOrderCreateModel()->addProduct($productId);
        }

        /**
         * Adding products to quote from special grid and
         */
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getOrderCreateModel()->addProducts(Zend_Json::decode($data));
        }

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', array());
            $this->_getOrderCreateModel()->updateQuoteItems($items);
        }

        /**
         * Remove quote item
         */
        if ( ($itemId = (int) $this->getRequest()->getPost('remove_item'))
             && ($from = (string) $this->getRequest()->getPost('from'))) {
            $this->_getOrderCreateModel($itemId)->removeItem($itemId, $from);
        }

        /**
         * Move quote item
         */
        if ( ($itemId = (int) $this->getRequest()->getPost('move_item'))
            && ($moveTo = (string) $this->getRequest()->getPost('to')) ) {
            $this->_getOrderCreateModel()->moveQuoteItem($itemId, $moveTo);
        }

        /*if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->_getOrderCreateModel()->setPaymentData($paymentData);
        }*/
        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
        }



        $this->_getOrderCreateModel()
            ->initRuleData()
            ->saveQuote();

        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Saving of giftmessages
         */
        if ($giftmessages = $this->getRequest()->getPost('giftmessage')) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)
                ->saveAllInQuote();
        }

        /**
         * Importing gift message allow items from specific product grid
         */
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(Zend_Json::decode($data));
        }

        /**
         * Importing gift message allow items on update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', array());
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }

        $data = $this->getRequest()->getPost('order');
        if (!empty($data['coupon']['code'])) {
            if ($this->_getQuote()->getCouponCode() !== $data['coupon']['code']) {
                $this->_getSession()->addError($this->__('"%s" coupon code is not valid.', $data['coupon']['code']));
            }
        }

        return $this;
    }

    /**
     * Index page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('left')->setIsCollapsed(true);

        $this->_initSession()
            ->_setActiveMenu('sales/order')
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_create'))
            ->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate(
                'sales/order/create/js.phtml'
            ))
            ->renderLayout();
    }


    public function reorderAction()
    {
//        $this->_initSession();
        $this->_getSession()->clear();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->getId()) {
            $order->setReordered(true);
            $this->_getOrderCreateModel()->initFromOrder($order);

            $this->_redirect('*/*');
        }
        else {
            $this->_redirect('*/sales_order/');
        }
    }

    protected function _reloadQuote()
    {
        $id = $this->_getQuote()->getId();
        $this->_getQuote()->load($id);
        return $this;
    }

    /**
     * Loading page block
     */
    public function loadBlockAction()
    {
        try {
            $this->_initSession()
                ->_processData();
        }
        catch (Mage_Core_Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e){
            $this->_reloadQuote();
//            $this->_getSession()->addException($e, $this->__('Processing data problem'));
            $this->_getSession()->addException($e, $e->getMessage());
        }


        $asJson= $this->getRequest()->getParam('json');
        $block = $this->getRequest()->getParam('block');
        $res = array();

        if ($block) {
            $blocks = explode(',', $block);

            if ($asJson && !in_array('messages', $blocks)) {
                $blocks[] = 'messages';
            }

            foreach ($blocks as $block) {
                $blockName = 'adminhtml/sales_order_create_'.$block;
                try {
                    $blockObject = $this->getLayout()->createBlock($blockName);
                    $res[$block] = $blockObject->toHtml();
                }
                catch (Exception $e){
                    $res[$block] = $this->__('Can not create block "%s"', $blockName);
                }
            }
        }

        if ($asJson) {
            $this->getResponse()->setBody(Zend_Json::encode($res));
        }
        else {
            $this->getResponse()->setBody(implode('', $res));
        }
    }

    /**
     * Start order create action
     */
    public function startAction()
    {
        $this->_getSession()->clear();
        $this->_redirect('*/*', array('customer_id' => $this->getRequest()->getParam('customer_id')));
    }

    /**
     * Cancel order create
     */
    public function cancelAction()
    {
        if ($orderId = $this->_getSession()->getReordered()) {
            $this->_getSession()->clear();
            $this->_redirect('*/sales_order/view', array(
                'order_id'=>$orderId
            ));
        } else {
            $this->_getSession()->clear();
            $this->_redirect('*/*');
        }

    }

    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        try {
            $this->_processData();
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

            $order = $this->_getOrderCreateModel()
                ->importPostData($this->getRequest()->getPost('order'))
                ->createOrder();

            $this->_getSession()->clear();
            $url = $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
        catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $url = $this->_redirect('*/*/');
        }
        catch (Exception $e){
            echo $e;
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $url = $this->_redirect('*/*/');
        }
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
                $aclResource = 'sales/order/actions/create';
                break;
            case 'reorder':
                $aclResource = 'sales/order/actions/reorder';
                break;
            case 'cancel':
                $aclResource = 'sales/order/actions/cancel';
                break;
            case 'save':
                $aclResource = 'sales/order/actions/edit';
                break;
            default:
                $aclResource = 'sales/order/actions';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }
}