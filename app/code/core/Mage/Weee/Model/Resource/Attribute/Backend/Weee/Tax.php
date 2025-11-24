<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/**
 * Catalog product WEEE tax backend attribute model
 *
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Defines main resource table and table identifier field
     */
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }

    /**
     * Load product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return array
     * @throws Mage_Core_Exception
     */
    public function loadProductData($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), [
                'website_id',
                'country',
                'state',
                'value',
            ])
            ->where('entity_id = ?', (int) $product->getId())
            ->where('attribute_id = ?', (int) $attribute->getId());
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id = ?', 0);
        } else {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $select->where('website_id IN (?)', [0, Mage::app()->getStore($storeId)->getWebsiteId()]);
            }
        }

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Delete product data
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $product
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function deleteProductData($product, $attribute)
    {
        $where = [
            'entity_id = ?'    => (int) $product->getId(),
            'attribute_id = ?' => (int) $attribute->getId(),
        ];

        $adapter   = $this->_getWriteAdapter();
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $where['website_id IN(?)'] =  [0, Mage::app()->getStore($storeId)->getWebsiteId()];
            }
        }

        $adapter->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * Insert product data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function insertProductData($product, $data)
    {
        $data['entity_id']      = (int) $product->getId();
        $data['entity_type_id'] = (int) $product->getEntityTypeId();

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}
