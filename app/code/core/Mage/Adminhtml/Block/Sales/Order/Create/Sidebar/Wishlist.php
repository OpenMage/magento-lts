<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create sidebar wishlist block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_wishlist_item';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_wishlist');
        $this->setDataId('wishlist');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Wishlist');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        if (is_null($collection)) {
            $collection = $this->getCreateOrderModel()->getCustomerWishlist(true);
            if ($collection) {
                $collection = $collection->getItemCollection()->load();
            }
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve all items
     *
     * @return array
     */
    public function getItems()
    {
        $items = parent::getItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            $item->setName($product->getName());
            $item->setPrice($product->getFinalPrice(1));
            $item->setTypeId($product->getTypeId());
        }
        return $items;
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param   Mage_Wishlist_Model_Item $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getProduct()->getId();
    }

    /**
     * Retrieve identifier of block item
     *
     * @param   Varien_Object $item
     * @return  int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * @return false|int
     */
    public function canDisplay()
    {
        if (!Mage::helper('wishlist')->isAllow()) {
            return false;
        }
        return parent::canDisplay();
    }

    /**
     * Retrieve possibility to display quantity column in grid of wishlist block
     *
     * @return bool
     */
    public function canDisplayItemQty()
    {
        return true;
    }
}
