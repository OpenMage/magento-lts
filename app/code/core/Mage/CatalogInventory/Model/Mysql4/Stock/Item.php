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
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock item resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Mysql4_Stock_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function  _construct()
    {
        $this->_init('cataloginventory/stock_item', 'item_id');
    }

    /**
     * Loading stock item data by product
     *
     * @param   Mage_CatalogInventory_Model_Stock_Item $item
     * @param   int $productId
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function loadByProductId(Mage_CatalogInventory_Model_Stock_Item $item, $productId)
    {
        $select = $this->_getLoadSelect('product_id', $productId, $item)
            ->where('stock_id=?', $item->getStockId());

        $item->setData($this->_getReadAdapter()->fetchRow($select));
        $this->_afterLoad($item);
        return $this;
    }

    /**
     * Retrieve select object and join it to product entity table to get type ids
     *
     * @param  string $field
     * @param  mixed $value
     * @param  object $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        return parent::_getLoadSelect($field, $value, $object)
            ->joinInner(array('p' => $this->getTable('catalog/product')), 'product_id=p.entity_id', 'type_id')
        ;
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Entity_Product_Collection $productCollection
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Item
     */
    public function addCatalogInventoryToProductCollection($productCollection)
    {
        $isStockManagedInConfig = (int) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $productCollection->joinTable('cataloginventory/stock_item',
            'product_id=entity_id',
            array(
                'is_saleable' => new Zend_Db_Expr('(IF(IF(use_config_manage_stock, ' . $isStockManagedInConfig . ', manage_stock), is_in_stock, 1))'),
                'inventory_in_stock' => 'is_in_stock'
            ),
            null, 'left');
        return $this;
    }
}
