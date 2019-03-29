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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Price indexer resource model
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice extends Mage_CatalogIndex_Model_Resource_Indexer_Abstract
{
    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('catalogindex/minimal_price', 'index_id');

        $this->_entityIdFieldName   = 'entity_id';
        $this->_storeIdFieldName    = 'store_id';
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $conditions
     * @return unknown
     */
    public function getMinimalValue($conditions)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('catalogindex/price'), 'MIN(value)');
        foreach ($conditions as $field=>$value) {
            $condition = "{$field} = ?";
            if (is_array($value))
                $condition = "{$field} in (?)";

            $select->where($condition, $value);
        }

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $productId
     * @param unknown_type $storeId
     * @param unknown_type $attributeId
     */
    public function cleanup($productId, $storeId = null, $attributeId = null)
    {
        $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_entityIdFieldName} = ?", $productId);

        if (!is_null($storeId))
            $conditions[] = $this->_getWriteAdapter()->quoteInto("{$this->_storeIdFieldName} = ?", $storeId);

        $conditions = implode (' AND ', $conditions);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }
}
