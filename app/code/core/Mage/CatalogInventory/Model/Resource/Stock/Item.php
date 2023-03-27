<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stock item resource model
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Resource_Stock_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cataloginventory/stock_item', 'item_id');
    }

    /**
     * Loading stock item data by product
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $item
     * @param int $productId
     * @return $this
     */
    public function loadByProductId(Mage_CatalogInventory_Model_Stock_Item $item, $productId)
    {
        $select = $this->_getLoadSelect('product_id', $productId, $item)
            ->where('stock_id = :stock_id');
        $data = $this->_getReadAdapter()->fetchRow($select, [':stock_id' => $item->getStockId()]);
        if ($data) {
            $item->setData($data);
        }
        $this->_afterLoad($item);
        return $this;
    }

    /**
     * Retrieve select object and join it to product entity table to get type ids
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_CatalogInventory_Model_Stock_Item $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        return parent::_getLoadSelect($field, $value, $object)
            ->join(
                ['p' => $this->getTable('catalog/product')],
                'product_id=p.entity_id',
                ['type_id']
            );
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return $this
     */
    public function addCatalogInventoryToProductCollection($productCollection)
    {
        $adapter = $this->_getReadAdapter();
        $isManageStock = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $stockExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 1', $isManageStock, 'cisi.manage_stock');
        $stockExpr = $adapter->getCheckSql("({$stockExpr} = 1)", 'cisi.is_in_stock', '1');

        $productCollection->joinTable(
            ['cisi' => 'cataloginventory/stock_item'],
            'product_id=entity_id',
            [
                'is_saleable' => new Zend_Db_Expr($stockExpr),
                'inventory_in_stock' => 'is_in_stock'
            ],
            null,
            'left'
        );
        return $this;
    }

    /**
     * Use qty correction for qty column update
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $object
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(Varien_Object $object, $table)
    {
        $data = parent::_prepareDataForTable($object, $table);
        if (!$object->isObjectNew() && $object->getQtyCorrection()) {
            $qty = abs($object->getQtyCorrection());
            if ($object->getQtyCorrection() < 0) {
                $data['qty'] = new Zend_Db_Expr('qty-' . $qty);
            } else {
                $data['qty'] = new Zend_Db_Expr('qty+' . $object->getQtyCorrection());
            }
        }
        return $data;
    }
}
