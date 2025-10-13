<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports Recently Viewed Products Block
 *
 * @package    Mage_Reports
 *
 * @method $this setRecentlyViewedProducts(Mage_Reports_Model_Resource_Product_Index_Collection_Abstract $value)
 * @method Mage_Reports_Model_Resource_Product_Index_Collection_Abstract getRecentlyViewedProducts()
 */
class Mage_Reports_Block_Product_Viewed extends Mage_Reports_Block_Product_Abstract
{
    public const XML_PATH_RECENTLY_VIEWED_COUNT    = 'catalog/recently_products/viewed_count';

    /**
     * Viewed Product Index model name
     *
     * @var string
     */
    protected $_indexName       = 'reports/product_index_viewed';

    /**
     * Retrieve page size (count)
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }

        return Mage::getStoreConfig(self::XML_PATH_RECENTLY_VIEWED_COUNT);
    }

    /**
     * Added predefined ids support
     */
    public function getCount()
    {
        $ids = $this->getProductIds();
        if (!empty($ids)) {
            return count($ids);
        }

        return parent::getCount();
    }

    /**
     * Prepare to html
     * check has viewed products
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCount()) {
            return '';
        }

        $this->setRecentlyViewedProducts($this->getItemsCollection());
        return parent::_toHtml();
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->getItemsCollection()),
        );
    }
}
