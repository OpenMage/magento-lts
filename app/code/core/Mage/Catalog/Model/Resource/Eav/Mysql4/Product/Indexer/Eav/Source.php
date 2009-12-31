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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Eav Select and Multiply Select Attributes Indexer resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Source
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Abstract
{
    /**
     * Initialize connection and define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav', 'entity_id');
    }

    /**
     * Retrieve indexable eav attribute ids
     *
     * @param bool $multiSelect
     * @return array
     */
    protected function _getIndexableAttributes($multiSelect)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('ca' => $this->getTable('catalog/eav_attribute')), 'attribute_id')
            ->join(
                array('ea' => $this->getTable('eav/attribute')),
                'ca.attribute_id = ea.attribute_id',
                array())
            ->where($this->_getIndexableAttributesCondition());

        if ($multiSelect == true) {
            $select->where('ea.backend_type = ?', 'varchar')
                ->where('ea.frontend_input = ?', 'multiselect');
        } else {
            $select->where('ea.backend_type = ?', 'int')
                ->where('ea.frontend_input = ?', 'select');
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Prepare data index for indexable attributes
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Decimal
     */
    protected function _prepareIndex($entityIds = null, $attributeId = null)
    {
        $this->_prepareSelectIndex($entityIds, $attributeId);
        $this->_prepareMultiselectIndex($entityIds, $attributeId);

        return $this;
    }


    /**
     * Prepare data index for indexable select attributes
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Source
     */
    protected function _prepareSelectIndex($entityIds = null, $attributeId = null)
    {
        $adapter    = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();
        // prepare select attributes
        if (is_null($attributeId)) {
            $attrIds    = $this->_getIndexableAttributes(false);
        } else {
            $attrIds    = array($attributeId);
        }

        if (!$attrIds) {
            return $this;
        }

        $select = $adapter->select()
            ->from(
                array('pid' => $this->getValueTable('catalog/product', 'int')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pis' => $this->getValueTable('catalog/product', 'int')),
                'pis.entity_id = pid.entity_id AND pis.attribute_id = pid.attribute_id'
                    . ' AND pis.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pis.value_id > 0, pis.value, pid.value)')))
            ->where('pid.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pid.attribute_id IN(?)', $attrIds)
            ->where('IF(pis.value_id > 0, pis.value, pid.value) IS NOT NULL');

        $statusCond = $adapter->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'pid.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pid.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('pid.entity_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($idxTable);
        $adapter->query($query);

        return $this;
    }

    /**
     * Prepare data index for indexable multiply select attributes
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareMultiselectIndex($entityIds = null, $attributeId = null)
    {
        $adapter    = $this->_getWriteAdapter();

        // prepare multiselect attributes
        if (is_null($attributeId)) {
            $attrIds    = $this->_getIndexableAttributes(true);
        } else {
            $attrIds    = array($attributeId);
        }

        if (!$attrIds) {
            return $this;
        }

        // load attribute options
        $options = array();
        $select  = $adapter->select()
            ->from($this->getTable('eav/attribute_option'), array('attribute_id', 'option_id'))
            ->where('attribute_id IN(?)', $attrIds);
        $query = $select->query();
        while ($row = $query->fetch()) {
            $options[$row['attribute_id']][$row['option_id']] = true;
        }

        // prepare get multiselect values query
        $select = $adapter->select()
            ->from(
                array('pvd' => $this->getValueTable('catalog/product', 'varchar')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pvs' => $this->getValueTable('catalog/product', 'varchar')),
                'pvs.entity_id = pvd.entity_id AND pvs.attribute_id = pvd.attribute_id'
                    . ' AND pvs.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pvs.value_id>0, pvs.value, pvd.value)')))
            ->where('pvd.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pvd.attribute_id IN(?)', $attrIds);

        $statusCond = $adapter->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'pvd.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pvd.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('pvd.entity_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $i     = 0;
        $data  = array();
        $query = $select->query();
        while ($row = $query->fetch()) {
            $values = explode(',', $row['value']);
            foreach ($values as $valueId) {
                if (isset($options[$row['attribute_id']][$valueId])) {
                    $data[] = array(
                        $row['entity_id'],
                        $row['attribute_id'],
                        $row['store_id'],
                        $valueId
                    );
                    $i ++;
                    if ($i % 10000 == 0) {
                        $this->_saveIndexData($data);
                        $data = array();
                    }
                }
            }
        }

        $this->_saveIndexData($data);
        unset($options);
        unset($data);

        return $this;
    }

    /**
     * Save a data to temporary source index table
     *
     * @param array $data
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Source
     */
    protected function _saveIndexData(array $data)
    {
        if (!$data) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $adapter->insertArray($this->getIdxTable(), array('entity_id', 'attribute_id', 'store_id', 'value'), $data);
        return $this;
    }
}
