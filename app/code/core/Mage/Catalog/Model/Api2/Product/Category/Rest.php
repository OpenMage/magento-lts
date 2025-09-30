<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Abstract API2 class for product categories
 *
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Model_Api2_Product_Category_Rest extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Product category assign is not available
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Product category update is not available
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $return = [];

        foreach ($this->_getCategoryIds() as $categoryId) {
            $return[] = ['category_id' => $categoryId];
        }
        return $return;
    }

    /**
     * Only admin have permissions for product category unassign
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Load category by id
     *
     * @param int $categoryId
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategoryById($categoryId)
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')->setStoreId(0)->load($categoryId);
        if (!$category->getId()) {
            $this->_critical('Category not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }

        return $category;
    }

    /**
     * Get assigned categories ids
     *
     * @return array
     */
    protected function _getCategoryIds()
    {
        return $this->_getProduct()->getCategoryCollection()->addIsActiveFilter()->getAllIds();
    }
}
