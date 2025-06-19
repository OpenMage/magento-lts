<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable samples resource collection
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Sample_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('downloadable/sample');
    }

    /**
     * Method for product filter
     *
     * @param Mage_Catalog_Model_Product|array|integer|null $product
     * @return $this
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', ['in' => $product]);
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Add title column to select
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId = 0)
    {
        $ifNullDefaultTitle = $this->getConnection()
            ->getIfNullSql('st.title', 'd.title');
        $this->getSelect()
            ->joinLeft(
                ['d' => $this->getTable('downloadable/sample_title')],
                'd.sample_id=main_table.sample_id AND d.store_id = 0',
                ['default_title' => 'title'],
            )
            ->joinLeft(
                ['st' => $this->getTable('downloadable/sample_title')],
                'st.sample_id=main_table.sample_id AND st.store_id = ' . (int) $storeId,
                ['store_title' => 'title','title' => $ifNullDefaultTitle],
            )
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }
}
