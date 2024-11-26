<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for product categories
 *
 * @category   Mage
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
