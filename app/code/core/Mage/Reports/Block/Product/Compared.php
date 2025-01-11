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
