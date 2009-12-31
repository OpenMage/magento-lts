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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Website Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define resource table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_website', 'product_id');
    }

    /**
     * Get catalog product resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _getProductResource()
    {
        return Mage::getResourceSingleton('catalog/product');
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website
     */
    public function removeProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0)
        {
            return $this;
        }

        $whereCond = array(
            $this->_getWriteAdapter()->quoteInto('website_id IN(?)', $websiteIds),
            $this->_getWriteAdapter()->quoteInto('product_id IN(?)', $productIds)
        );
        $whereCond = join(' AND ', $whereCond);

        $this->_getWriteAdapter()->beginTransaction();
        try {
            $this->_getWriteAdapter()->delete($this->getMainTable(), $whereCond);
            $this->_getWriteAdapter()->commit();
        }
        catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Website
     */
    public function addProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0)
        {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        // Before adding of products we should remove it old rows with same ids
        $this->removeProducts($websiteIds, $productIds);

        foreach ($websiteIds as $websiteId) {
            foreach ($productIds as $productId) {
                if (!$productId) {
                    continue;
                }
                $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                    'product_id' => $productId,
                    'website_id' => $websiteId
                ));
            }

            // Refresh product enabled index
            $storeIds = Mage::app()->getWebsite($websiteId)->getStoreIds();
            foreach ($storeIds as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $this->_getProductResource()->refreshEnabledIndex($store, $productIds);
            }
        }

        $this->_getWriteAdapter()->commit();

        return $this;
    }

    /**
     * Retrieve product(s) website ids.
     *
     * @param array $productIds
     * @return array
     */
    public function getWebsites($productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('product_id', 'website_id'))
            ->where('product_id IN (?)', $productIds);
        $rowset  = $this->_getReadAdapter()->fetchAll($select);

        $result = array();
        foreach ($rowset as $row) {
            $result[$row['product_id']][] = $row['website_id'];
        }

        return $result;
    }
}
