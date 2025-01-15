<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Compared Product Index Model
 *
 * @category   Mage
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Compared _getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Compared getResource()
 * @method $this setVisitorId(int $value)
 * @method $this setCustomerId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method $this setStoreId(int $value)
 * @method string getAddedAt()
 * @method $this setAddedAt(string $value)
 */
class Mage_Reports_Model_Product_Index_Compared extends Mage_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_compared_count';

    protected function _construct()
    {
        $this->_init('reports/product_index_compared');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = [];

        /** @var Mage_Catalog_Helper_Product_Compare $helper */
        $helper = Mage::helper('catalog/product_compare');

        if ($helper->hasItems()) {
            foreach ($helper->getItemCollection() as $item) {
                $productIds[] = $item->getEntityId();
            }
        }

        if (Mage::registry('current_product')) {
            $productIds[] = Mage::registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
