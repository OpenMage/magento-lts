<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog composite product configuration controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Customer_Cart_Product_Composite_CartController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'customer/manage';

    /**
     * Customer we're working with
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Quote we're working with
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Quote item we're working with
     *
     * @var Mage_Sales_Model_Quote_Item
     */
    protected $_quoteItem = null;

    /**
     * Loads customer, quote and quote item by request params
     *
     * @return $this
     */
    protected function _initData()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            Mage::throwException($this->__('No customer id defined.'));
        }

        $this->_customer = Mage::getModel('customer/customer')
            ->load($customerId);

        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');

        $this->_quote = Mage::getModel('sales/quote')
            ->setWebsite(Mage::app()->getWebsite($websiteId))
            ->loadByCustomer($this->_customer);

        $this->_quoteItem = $this->_quote->getItemById($quoteItemId);
        if (!$this->_quoteItem) {
            Mage::throwException($this->__('Wrong quote item.'));
        }

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     *
     * @return $this
     */
    public function configureAction()
    {
        $configureResult = new Varien_Object();
        try {
            $this->_initData();

            $quoteItem = $this->_quoteItem;

            $optionCollection = Mage::getModel('sales/quote_item_option')
                ->getCollection()
                ->addItemFilter($quoteItem);
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setOk(true);
            $configureResult->setProductId($quoteItem->getProductId());
            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setCurrentCustomer($this->_customer);
        } catch (Exception $exception) {
            $configureResult->setError(true);
            $configureResult->setMessage($exception->getMessage());
        }

        /** @var Mage_Adminhtml_Helper_Catalog_Product_Composite $helper */
        $helper = Mage::helper('adminhtml/catalog_product_composite');
        // During order creation in the backend admin has ability to add any products to order
        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * IFrame handler for submitted configuration for quote item
     *
     * @return $this
     */
    public function updateAction()
    {
        $updateResult = new Varien_Object();
        try {
            $this->_initData();

            $buyRequest = new Varien_Object($this->getRequest()->getParams());
            $this->_quote->updateItem($this->_quoteItem->getId(), $buyRequest);
            $this->_quote->collectTotals()
                ->save();

            $updateResult->setOk(true);
        } catch (Exception $exception) {
            $updateResult->setError(true);
            $updateResult->setMessage($exception->getMessage());
        }

        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        Mage::getSingleton('adminhtml/session')->setCompositeProductResult($updateResult);
        $this->_redirect('*/catalog_product/showUpdateResult');

        return $this;
    }
}
