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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products lowstock report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class Mage_Reports_Model_Mysql4_Product_Lowstock_Collection extends Mage_Reports_Model_Mysql4_Product_Collection
{
	protected $_inventoryItemResource = null;
	protected $_inventoryItemJoined = false;
	protected $_inventoryItemTableAlias = 'lowstock_inventory_item';
	
	/**
	 * @return string
	 */
	protected function _getInventoryItemResource() 
	{
		if (is_null($this->_inventoryItemResource)) {
			$this->_inventoryItemResource = Mage::getResourceSingleton('cataloginventory/stock_item');
		}
		return $this->_inventoryItemResource;
    }

    /**
	 * @return string
	 */
	protected function _getInventoryItemTable() 
	{
		return $this->_getInventoryItemResource()->getMainTable();
    }

    /**
	 * @return string
	 */
	protected function _getInventoryItemIdField() 
	{
		return $this->_getInventoryItemResource()->getIdFieldName();
	}
	
	/**
	 * @return string
	 */
	protected function _getInventoryItemTableAlias() 
	{
		return $this->_inventoryItemTableAlias;
	}

	/**
	 * @param array|string $fields
	 * @return string
	 */
	protected function _processInventoryItemFields($fields) 
	{
		if (is_array($fields)) {
			$aliasArr = array();
			foreach ($fields as &$field) {
				if ( is_string($field) && strpos($field, '(') === false ) {
					$field = sprintf('%s.%s', $this->_getInventoryItemTableAlias(), $field);
				}   
			}
			unset($field);
			return $fields;
		}
		return sprintf('%s.%s', $this->_getInventoryItemTableAlias(), $fields);
	}
	
	/**
	 * Join cataloginventory_stock_item table for further
	 * stock_item values filters
	 * @return Mage_Reports_Model_Mysql4_Product_Collection
	 */
	public function joinInventoryItem($fields=array()) {
		if ( !$this->_inventoryItemJoined ) {
			$this->getSelect()->join(
                array($this->_getInventoryItemTableAlias() => $this->_getInventoryItemTable()),
				sprintf('e.%s=%s.product_id',
					$this->getEntity()->getEntityIdField(),
					$this->_getInventoryItemTableAlias()
				),
				array()
			);
			$this->_inventoryItemJoined = true;
		}
        if (is_string($fields)) {
            $fields = array($fields);
        }
        if (!empty($fields)) {
            $this->getSelect()->columns($this->_processInventoryItemFields($fields));
        }
		return $this;
	}
	
	/**
	 * @param array|string $typeFilter
	 * @return Mage_Reports_Model_Mysql4_Product_Collection
	 */
	public function filterByProductType($typeFilter)
	{
		if (!is_string($typeFilter) && !is_array($typeFilter)) {
			Mage::throwException(
				Mage::helper('catalog')->__('Wrong product type filter specified')
			);
		}
		$this->addAttributeToFilter('type_id', $typeFilter);
		return $this;
	}
	
	/**
	 * @return Mage_Reports_Model_Mysql4_Product_Collection
	 */
	public function filterByIsQtyProductTypes() 
	{
		$this->filterByProductType(
			array_keys(array_filter(Mage::helper('cataloginventory')->getIsQtyTypeIds()))
		);
		return $this;
	}
	
	/**
	 * @param int|null $storeId
	 * @return Mage_Reports_Model_Mysql4_Product_Collection
	 */
	public function useManageStockFilter($storeId=null)
	{
		$this->joinInventoryItem();
		$this->getSelect()->where(sprintf('IF(%s,%d,%s)=1', 
			$this->_processInventoryItemFields('use_config_manage_stock'), 
            (int) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,$storeId), 
            $this->_processInventoryItemFields('manage_stock')));
        return $this;
	}
	
	/**
	 * @param int|null $storeId
	 * @return Mage_Reports_Model_Mysql4_Product_Collection
	 */
	public function useNotifyStockQtyFilter($storeId=null)
	{
		$this->joinInventoryItem(array('qty'));
		$this->getSelect()->where(sprintf('qty < IF(%s,%d,%s)', 
			$this->_processInventoryItemFields('use_config_notify_stock_qty'), 
            (int) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY,$storeId), 
            $this->_processInventoryItemFields('notify_stock_qty')));
        return $this;
	}
}
