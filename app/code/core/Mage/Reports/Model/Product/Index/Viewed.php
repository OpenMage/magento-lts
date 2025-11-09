<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Catalog Viewed Product Index
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed _getResource()
 * @method string getAddedAt()
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed_Collection getCollection()
 * @method int getProductId()
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Viewed_Collection getResourceCollection()
 * @method $this setAddedAt(string $value)
 * @method $this setCustomerId(int $value)
 * @method $this setProductId(int $value)
 * @method $this setStoreId(int $value)
 * @method $this setVisitorId(int $value)
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
