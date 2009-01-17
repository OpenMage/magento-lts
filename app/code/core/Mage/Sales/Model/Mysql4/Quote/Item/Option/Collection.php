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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Item option collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Quote_Item_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_item_option');
    }

    /**
     * Apply quote item(s) filter to collection
     *
     * @param   int | array $item
     * @return  Mage_Sales_Model_Mysql4_Quote_Item_Option_Collection
     */
    public function addItemFilter($item)
    {
        if (empty($item)) {
            $this->addFieldToFilter('item_id', '');
        }
        elseif (is_array($item)) {
            $this->addFieldToFilter('item_id', array('in'=>$item));
        }
        elseif ($item instanceof Mage_Sales_Model_Quote_Item) {
        	$this->addFieldToFilter('item_id', $item->getId());
        }
        else {
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
        $ids = array();
        foreach ($this as $item) {
        	$ids[] = $item->getProductId();
        }
        return array_unique($ids);
    }

    /**
     * Get all option for item
     *
     * @param   mixed $item
     * @return  array
     */
    public function getOptionsByItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $itemId = $item->getId();
        }
        else {
            $itemId = $item;
        }

        $options = array();
        foreach ($this as $option) {
        	if ($option->getItemId() == $itemId) {
        	    $options[] = $option;
        	}
        }
        return $options;
    }

    /**
     * Get all option for item
     *
     * @param   mixed $item
     * @return  array
     */
    public function getOptionsByProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        }
        else {
            $productId = $product;
        }

        $options = array();
        foreach ($this as $option) {
        	if ($option->getProductId() == $productId) {
        	    $options[] = $option;
        	}
        }
        return $options;
    }
}