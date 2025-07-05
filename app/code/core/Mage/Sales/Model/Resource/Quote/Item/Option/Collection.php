<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Item option collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Item_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Array of option ids grouped by item id
     *
     * @var array
     */
    protected $_optionsByItem        = [];

    /**
     * Array of option ids grouped by product id
     *
     * @var array
     */
    protected $_optionsByProduct     = [];

    /**
     * Define resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('sales/quote_item_option');
    }

    /**
     * Fill array of options by item and product
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $option) {
            $optionId   = $option->getId();
            $itemId     = $option->getItemId();
            $productId  = $option->getProductId();
            if (isset($this->_optionsByItem[$itemId])) {
                $this->_optionsByItem[$itemId][] = $optionId;
            } else {
                $this->_optionsByItem[$itemId] = [$optionId];
            }
            if (isset($this->_optionsByProduct[$productId])) {
                $this->_optionsByProduct[$productId][] = $optionId;
            } else {
                $this->_optionsByProduct[$productId] = [$optionId];
            }
        }

        return $this;
    }

    /**
     * Apply quote item(s) filter to collection
     *
     * @param int | array $item
     * @return $this
     */
    public function addItemFilter($item)
    {
        if (empty($item)) {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        } elseif (is_array($item)) {
            $this->addFieldToFilter('item_id', ['in' => $item]);
        } elseif ($item instanceof Mage_Sales_Model_Quote_Item) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->addFieldToFilter('item_id', $item);
        }

        return $this;
    }

    /**
     * Get array of all product ids
     *
     * @return array
     */
    public function getProductIds()
    {
        $this->load();

        return array_keys($this->_optionsByProduct);
    }

    /**
     * Get all option for item
     *
     * @param mixed $item
     * @return array
     */
    public function getOptionsByItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $itemId = $item->getId();
        } else {
            $itemId = $item;
        }

        $this->load();

        $options = [];
        if (isset($this->_optionsByItem[$itemId])) {
            foreach ($this->_optionsByItem[$itemId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }

    /**
     * Get all option for item
     *
     * @param int | Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOptionsByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $this->load();

        $options = [];
        if (isset($this->_optionsByProduct[$productId])) {
            foreach ($this->_optionsByProduct[$productId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }
}
