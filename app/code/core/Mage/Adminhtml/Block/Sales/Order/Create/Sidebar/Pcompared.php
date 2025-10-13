<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create sidebar recently compared block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Pcompared extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_pcompared');
        $this->setDataId('pcompared');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Recently Compared Products');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $productCollection = $this->getData('item_collection');
        if (is_null($productCollection)) {
            // get products to skip
            $skipProducts = [];
            if ($collection = $this->getCreateOrderModel()->getCustomerCompareList()) {
                $collection = $collection->getItemCollection()
                    ->useProductItem(true)
                    ->setStoreId($this->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->load();
                foreach ($collection as $item) {
                    $skipProducts[] = $item->getProductId();
                }
            }

            // prepare products collection and apply visitors log to it
            $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->setStoreId($this->getQuote()->getStoreId())
                ->addStoreFilter($this->getQuote()->getStoreId())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image');
            Mage::getResourceSingleton('reports/event')->applyLogToCollection(
                $productCollection,
                Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
                $this->getCustomerId(),
                0,
                $skipProducts,
            );

            $productCollection->load();
            $this->setData('item_collection', $productCollection);
        }

        return $productCollection;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * Get product Id
     *
     * @param Mage_Catalog_Model_Product $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier of block item
     *
     * @param   mixed $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getId();
    }
}
