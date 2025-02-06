<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Reports Recently Compared Products Block
 *
 * @category   Mage
 * @package    Mage_Reports
 *
 * @method $this setRecentlyComparedProducts(Mage_Reports_Model_Resource_Product_Index_Collection_Abstract $value)
 */
class Mage_Reports_Block_Product_Compared extends Mage_Reports_Block_Product_Abstract
{
    public const XML_PATH_RECENTLY_COMPARED_COUNT  = 'catalog/recently_products/compared_count';

    /**
     * Compared Product Index model name
     *
     * @var string
     */
    protected $_indexName = 'reports/product_index_compared';

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
        return Mage::getStoreConfig(self::XML_PATH_RECENTLY_COMPARED_COUNT);
    }

    /**
     * Prepare to html
     * Check has compared products
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCount()) {
            return '';
        }

        $this->setRecentlyComparedProducts($this->getItemsCollection());

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
