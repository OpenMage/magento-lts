<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Catalog Viewed Product Index
 *
 * @category   Mage
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed _getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed getResource()
 * @method $this setVisitorId(int $value)
 * @method $this setCustomerId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method $this setStoreId(int $value)
 * @method string getAddedAt()
 * @method $this setAddedAt(string $value)
 */
class Mage_Reports_Model_Product_Index_Viewed extends Mage_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_viewed_count';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('reports/product_index_viewed');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = [];

        if (Mage::registry('current_product')) {
            $productIds[] = Mage::registry('current_product')->getId();
        }

        return $productIds;
    }
}
