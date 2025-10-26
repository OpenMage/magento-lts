<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order view block
 *
 * @package    Mage_Sales
 *
 * @method int getCustomerId()
 * @method Mage_Sales_Model_Resource_Order_Collection getOrders()
 * @method $this setOrders(Mage_Sales_Model_Resource_Order_Collection $value)
 */
class Mage_Sales_Block_Reorder_Sidebar extends Mage_Core_Block_Template
{
    /**
     * Init orders and templates
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->setTemplate('sales/order/history.phtml');
            $this->initOrders();
        }
    }

    /**
     * Init customer order for display on front
     */
    public function initOrders()
    {
        $customerId = $this->getCustomerId() ? $this->getCustomerId()
            : $this->_getCustomerSession()->getCustomer()->getId();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter(
                'state',
                ['in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()],
            )
            ->addAttributeToSort('created_at', 'desc')
            ->setPage(1, 1);
        //TODO: add filter by current website

        $this->setOrders($orders);
    }

    /**
     * Get list of last ordered products
     *
     * @return array
     */
    public function getItems()
    {
        $items = [];
        $order = $this->getLastOrder();
        $limit = 5;

        if ($order) {
            $website = Mage::app()->getStore()->getWebsiteId();
            foreach ($order->getParentItemsRandomCollection($limit) as $item) {
                if ($item->getProduct() && in_array($website, $item->getProduct()->getWebsiteIds())) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Check item product availability for reorder
     *
     * @return bool
     */
    public function isItemAvailableForReorder(Mage_Sales_Model_Order_Item $orderItem)
    {
        if ($orderItem->getProduct()) {
            return $orderItem->getProduct()->getStockItem()->getIsInStock();
        }

        return false;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/addgroup', ['_secure' => true]);
    }

    /**
     * Last order getter
     *
     * @return Mage_Sales_Model_Order|bool
     */
    public function getLastOrder()
    {
        if (!$this->getOrders()) {
            return false;
        }

        foreach ($this->getOrders() as $order) {
            return $order;
        }

        return false;
    }

    /**
     * Render "My Orders" sidebar block
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_getCustomerSession()->isLoggedIn() || $this->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * Retrieve customer session instance
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getItemProducts()),
        );
    }

    /**
     * Retrieve products list from items
     *
     * @return array
     */
    protected function _getItemProducts()
    {
        $products =  [];
        foreach ($this->getItems() as $item) {
            $products[] = $item->getProduct();
        }

        return $products;
    }
}
