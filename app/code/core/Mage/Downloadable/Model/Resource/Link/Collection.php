<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable links resource collection
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Resource_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('downloadable/link');
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
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', ['in' => $product]);
        }

        return $this;
    }

    /**
     * Retrieve title for for current store
     *
     * @param integer $storeId
     * @return $this
     */
    public function addTitleToResult($storeId = 0)
    {
        $ifNullDefaultTitle = $this->getConnection()
            ->getIfNullSql('st.title', 'd.title');
        $this->getSelect()
            ->joinLeft(
                ['d' => $this->getTable('downloadable/link_title')],
                'd.link_id=main_table.link_id AND d.store_id = 0',
                ['default_title' => 'title']
            )
            ->joinLeft(
                ['st' => $this->getTable('downloadable/link_title')],
                'st.link_id=main_table.link_id AND st.store_id = ' . (int)$storeId,
                ['store_title' => 'title','title' => $ifNullDefaultTitle]
            )
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }

    /**
     * Retrieve price for for current website
     *
     * @param integer $websiteId
     * @return $this
     */
    public function addPriceToResult($websiteId)
    {
        $ifNullDefaultPrice = $this->getConnection()
            ->getIfNullSql('stp.price', 'dp.price');
        $this->getSelect()
            ->joinLeft(
                ['dp' => $this->getTable('downloadable/link_price')],
                'dp.link_id=main_table.link_id AND dp.website_id = 0',
                ['default_price' => 'price']
            )
            ->joinLeft(
                ['stp' => $this->getTable('downloadable/link_price')],
                'stp.link_id=main_table.link_id AND stp.website_id = ' . (int)$websiteId,
                ['website_price' => 'price','price' => $ifNullDefaultPrice]
            );

        return $this;
    }
}
