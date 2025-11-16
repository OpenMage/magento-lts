<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap resource product collection model
 *
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Resource_Catalog_Product extends Mage_Sitemap_Model_Resource_Catalog_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Get product collection array
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
            ->from(['main_table' => $this->getMainTable()], [$this->getIdFieldName()])
            ->join(
                ['w' => $this->getTable('catalog/product_website')],
                'main_table.entity_id = w.product_id',
                [],
            )
            ->where('w.website_id=?', $store->getWebsiteId());

        $storeId = (int) $store->getId();

        $urlRewrite = $this->_factory->getProductUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($this->_select, $storeId);

        $this->_addFilter(
            $storeId,
            'visibility',
            Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(),
            'in',
        );
        $this->_addFilter(
            $storeId,
            'status',
            Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(),
            'in',
        );

        return $this->_loadEntities();
    }

    /**
     * Prepare product
     *
     * @return Varien_Object
     * @deprecated after 1.7.0.2
     */
    protected function _prepareProduct(array $productRow)
    {
        return $this->_prepareObject($productRow);
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
        return !empty($row['request_path']) ? $row['request_path'] : 'catalog/product/view/id/' . $entity->getId();
    }

    /**
     * Loads product attribute by given attribute code
     *
     * @param string $attributeCode
     * @return Mage_Sitemap_Model_Resource_Catalog_Abstract
     */
    protected function _loadAttribute($attributeCode)
    {
        $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

        $this->_attributesCache[$attributeCode] = [
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id'   => $attribute->getId(),
            'table'          => $attribute->getBackend()->getTable(),
            'is_global'      => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'backend_type'   => $attribute->getBackendType(),
        ];
        return $this;
    }
}
