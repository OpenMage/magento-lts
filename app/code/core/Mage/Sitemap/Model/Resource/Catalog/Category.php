<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource catalog collection model
 *
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Catalog_Category extends Mage_Sitemap_Model_Resource_Catalog_Abstract
{
    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
    }

    /**
     * Get category collection array
     *
     * @param int $storeId
     * @return array|false
     */
    public function getCollection($storeId)
    {
        $store = Mage::app()->getStore($storeId);
        if (!$store) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=?', $store->getRootCategoryId());

        $categoryRow = $this->_getWriteAdapter()->fetchRow($this->_select);
        if (!$categoryRow) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from(['main_table' => $this->getMainTable()], [$this->getIdFieldName()])
            ->where('main_table.path LIKE ?', $categoryRow['path'] . '/%');

        $storeId = (int) $store->getId();

        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($this->_select, $storeId);

        $this->_addFilter($storeId, 'is_active', 1);

        return $this->_loadEntities();
    }

    /**
     * Prepare category
     *
     * @return Varien_Object
     * @deprecated after 1.7.0.2
     */
    protected function _prepareCategory(array $categoryRow)
    {
        return $this->_prepareObject($categoryRow);
    }

    /**
     * Retrieve entity url
     *
     * @param array $row
     * @param Varien_Object $entity
     * @return string
     */
    protected function _getEntityUrl($row, $entity)
    {
        return !empty($row['request_path']) ? $row['request_path'] : 'catalog/category/view/id/' . $entity->getId();
    }

    /**
     * Loads category attribute by given attribute code.
     *
     * @param string $attributeCode
     * @return $this
     */
    protected function _loadAttribute($attributeCode)
    {
        $attribute = Mage::getSingleton('catalog/category')->getResource()->getAttribute($attributeCode);

        $this->_attributesCache[$attributeCode] = [
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id'   => $attribute->getId(),
            'table'          => $attribute->getBackend()->getTable(),
            'is_global'      => $attribute->getIsGlobal(),
            'backend_type'   => $attribute->getBackendType(),
        ];
        return $this;
    }
}
