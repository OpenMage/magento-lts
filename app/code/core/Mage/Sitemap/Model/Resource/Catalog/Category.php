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
 * @package     Mage_Sitemap
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sitemap resource catalog collection model
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @return array
     */
    public function getCollection($storeId)
    {
        /* @var $store Mage_Core_Model_Store */
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
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
            ->where('main_table.path LIKE ?', $categoryRow['path'] . '/%');

        $storeId = (int)$store->getId();

        /** @var $urlRewrite Mage_Catalog_Helper_Category_Url_Rewrite_Interface */
        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($this->_select, $storeId);

        $this->_addFilter($storeId, 'is_active', 1);

        return $this->_loadEntities();
    }

    /**
     * Prepare category
     *
     * @deprecated after 1.7.0.2
     *
     * @param array $categoryRow
     * @return Varien_Object
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
     * @return Mage_Sitemap_Model_Resource_Catalog_Abstract
     */
    protected function _loadAttribute($attributeCode)
    {
        $attribute = Mage::getSingleton('catalog/category')->getResource()->getAttribute($attributeCode);

        $this->_attributesCache[$attributeCode] = array(
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id'   => $attribute->getId(),
            'table'          => $attribute->getBackend()->getTable(),
            'is_global'      => $attribute->getIsGlobal(),
            'backend_type'   => $attribute->getBackendType()
        );
        return $this;
    }
}
