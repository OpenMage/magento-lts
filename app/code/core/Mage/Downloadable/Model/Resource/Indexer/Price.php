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
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable products Price indexer resource model
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Resource_Indexer_Price extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->_prepareFinalPriceData();
            $this->_applyCustomOption();
            $this->_applyDownloadableLink();
            $this->_movePriceDataToIndexTable();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareFinalPriceData($entityIds);
        $this->_applyCustomOption();
        $this->_applyDownloadableLink();
        $this->_movePriceDataToIndexTable();

        return $this;
    }

    /**
     * Retrieve downloadable links price temporary index table name
     *
     * @see _prepareDefaultFinalPriceTable()
     *
     * @return string
     */
    protected function _getDownloadableLinkPriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('downloadable/product_price_indexer_idx');
        }
        return $this->getTable('downloadable/product_price_indexer_tmp');
    }

    /**
     * Prepare downloadable links price temporary index table
     *
     * @return $this
     */
    protected function _prepareDownloadableLinkPriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getDownloadableLinkPriceTable());
        return $this;
    }

    /**
     * Calculate and apply Downloadable links price to index
     *
     * @return $this
     */
    protected function _applyDownloadableLink()
    {
        $write  = $this->_getWriteAdapter();
        $table  = $this->_getDownloadableLinkPriceTable();

        $this->_prepareDownloadableLinkPriceTable();

        $dlType = $this->_getAttribute('links_purchased_separately');

        $ifPrice = $write->getIfNullSql('dlpw.price_id', 'dlpd.price');

        $select = $write->select()
            ->from(
                ['i' => $this->_getDefaultFinalPriceTable()],
                ['entity_id', 'customer_group_id', 'website_id']
            )
            ->join(
                ['dl' => $dlType->getBackend()->getTable()],
                "dl.entity_id = i.entity_id AND dl.attribute_id = {$dlType->getAttributeId()}"
                    . " AND dl.store_id = 0",
                []
            )
            ->join(
                ['dll' => $this->getTable('downloadable/link')],
                'dll.product_id = i.entity_id',
                []
            )
            ->join(
                ['dlpd' => $this->getTable('downloadable/link_price')],
                'dll.link_id = dlpd.link_id AND dlpd.website_id = 0',
                []
            )
            ->joinLeft(
                ['dlpw' => $this->getTable('downloadable/link_price')],
                'dlpd.link_id = dlpw.link_id AND dlpw.website_id = i.website_id',
                []
            )
            ->where('dl.value = ?', 1)
            ->group(['i.entity_id', 'i.customer_group_id', 'i.website_id'])
            ->columns([
                'min_price' => new Zend_Db_Expr('MIN('.$ifPrice.')'),
                'max_price' => new Zend_Db_Expr('SUM('.$ifPrice.')')
            ]);

        $query = $select->insertFromSelect($table);
        $write->query($query);

        $ifTierPrice = $write->getCheckSql('i.tier_price IS NOT NULL', '(i.tier_price + id.min_price)', 'NULL');
        $ifGroupPrice = $write->getCheckSql('i.group_price IS NOT NULL', '(i.group_price + id.min_price)', 'NULL');

        $select = $write->select()
            ->join(
                ['id' => $table],
                'i.entity_id = id.entity_id AND i.customer_group_id = id.customer_group_id'
                    .' AND i.website_id = id.website_id',
                []
            )
            ->columns([
                'min_price'   => new Zend_Db_Expr('i.min_price + id.min_price'),
                'max_price'   => new Zend_Db_Expr('i.max_price + id.max_price'),
                'tier_price'  => new Zend_Db_Expr($ifTierPrice),
                'group_price' => new Zend_Db_Expr($ifGroupPrice),
            ]);

        $query = $select->crossUpdateFromSelect(['i' => $this->_getDefaultFinalPriceTable()]);
        $write->query($query);

        $write->delete($table);

        return $this;
    }
}
