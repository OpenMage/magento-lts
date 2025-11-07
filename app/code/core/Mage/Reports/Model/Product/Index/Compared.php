<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Catalog Compared Product Index Model
 *
 * @package    Mage_Reports
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Compared _getResource()
 * @method string getAddedAt()
 * @method Mage_Reports_Model_Resource_Product_Index_Compared_Collection getCollection()
 * @method int getProductId()
 * @method Mage_Reports_Model_Resource_Product_Index_Compared getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Compared_Collection getResourceCollection()
 * @method $this setAddedAt(string $value)
 * @method $this setCustomerId(int $value)
 * @method $this setProductId(int $value)
 * @method $this setStoreId(int $value)
 * @method $this setVisitorId(int $value)
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
