<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create sidebar compared block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Compared extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_compared');
        $this->setDataId('compared');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Products in Comparison List');
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
            if ($collection = $this->getCreateOrderModel()->getCustomerCompareList()) {
                $collection = $collection->getItemCollection()
                    ->useProductItem(true)
                    ->setStoreId($this->getQuote()->getStoreId())
                    ->addStoreFilter($this->getQuote()->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('image')
                    ->addAttributeToSelect('status')
                    ->load();
            }
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    public function getItemId($item)
    {
        return $item->getCatalogCompareItemId();
    }
}
