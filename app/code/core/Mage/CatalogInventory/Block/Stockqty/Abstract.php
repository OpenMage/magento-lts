<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Product stock qty abstarct block
 *
 * @package    Mage_CatalogInventory
 */
abstract class Mage_CatalogInventory_Block_Stockqty_Abstract extends Mage_Core_Block_Template
{
    public const XML_PATH_STOCK_THRESHOLD_QTY = 'cataloginventory/options/stock_threshold_qty';

    /**
     * Retrieve current product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve current product stock qty
     *
     * @return float
     */
    public function getStockQty()
    {
        if (!$this->hasData('product_stock_qty')) {
            $qty = 0;
            if ($stockItem = $this->_getProduct()->getStockItem()) {
                $qty = (float) $stockItem->getStockQty();
            }

            $this->setData('product_stock_qty', $qty);
        }

        return $this->getData('product_stock_qty');
    }

    /**
     * Retrieve threshold of qty to display stock qty message
     *
     * @return string
     */
    public function getThresholdQty()
    {
        if (!$this->hasData('threshold_qty')) {
            $qty = Mage::getStoreConfigAsFloat(self::XML_PATH_STOCK_THRESHOLD_QTY);
            $this->setData('threshold_qty', $qty);
        }

        return $this->getData('threshold_qty');
    }

    /**
     * Retrieve id of message placeholder in template
     *
     * @return string
     */
    public function getPlaceholderId()
    {
        return 'stock-qty-' . $this->_getProduct()->getId();
    }

    /**
     * Retrieve visibility of stock qty message
     *
     * @return bool
     */
    public function isMsgVisible()
    {
        return ($this->getStockQty() > 0 && $this->getStockQty() <= $this->getThresholdQty());
    }
}
