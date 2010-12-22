<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Default Product Type Price Indexer Resource model
 *
 * For correctly work need define product type id
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Abstract
    implements Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
{
    /**
     * Product type code
     *
     * @var string
     */
    protected $_typeId;

    /**
     * Product Type is composite flag
     *
     * @var bool
     */
    protected $_isComposite = false;

    /**
     * Define main price index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_price', 'entity_id');
    }

    /**
     * Set Product Type code
     *
     * @param string $typeCode
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    public function setTypeId($typeCode)
    {
        $this->_typeId = $typeCode;
        return $this;
    }

    /**
     * Retrieve Product Type Code
     *
     * @return string
     */
    public function getTypeId()
    {
        if (is_null($this->_typeId)) {
            Mage::throwException(Mage::helper('catalog')->__('A product type is not defined for the indexer.'));
        }
        return $this->_typeId;
    }

    /**
     * Set Product Type Composite flag
     *
     * @param bool $flag
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    public function setIsComposite($flag)
    {
        $this->_isComposite = (bool)$flag;
        return $this;
    }

    /**
     * Check product type is composite
     *
     * @return bool
     */
    public function getIsComposite()
    {
        return $this->_isComposite;
    }

    /**
     * Reindex temporary (price result data) for all products
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->_prepareFinalPriceData();
        $this->_applyCustomOption();
        $this->_movePriceDataToIndexTable();
        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    public function reindexEntity($entityIds)
    {
        $this->useDisableKeys(false); 
        $this->_prepareFinalPriceData($entityIds);
        $this->_applyCustomOption();
        $this->_movePriceDataToIndexTable();
        $this->useDisableKeys(true);

        return $this;
    }

    /**
     * Retrieve final price temporary index table name
     *
     * @see _prepareDefaultFinalPriceTable()
     * @return string
     */
    protected function _getDefaultFinalPriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_final_idx');
        }
        return $this->getTable('catalog/product_price_indexer_final_tmp');
    }

    /**
     * Prepare final price temporary index table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareDefaultFinalPriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getDefaultFinalPriceTable());
        return $this;
    }

    /**
     * Retrieve website current dates table name
     *
     * @return string
     */
    protected function _getWebsiteDateTable()
    {
        return $this->getTable('catalog/product_index_website');
    }

    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->_prepareDefaultFinalPriceTable();

        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                '',
                array('customer_group_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                '',
                array('website_id'))
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array())
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id',
                array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'csg.default_store_id = cs.store_id AND cs.store_id != 0',
                array())
            ->join(
                array('pw' => $this->getTable('catalog/product_website')),
                'pw.product_id = e.entity_id AND pw.website_id = cw.website_id',
                array())
            ->joinLeft(
                array('tp' => $this->_getTierPriceIndexTable()),
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                    . ' AND tp.customer_group_id = cg.customer_group_id',
                array())
            ->where('e.type_id=?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);

        $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        $select->columns(array('tax_class_id' => $taxClassId));

        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $curentDate     = new Zend_Db_Expr('cwd.date');

        $finalPrice     = new Zend_Db_Expr("IF(IF({$specialFrom} IS NULL, 1, "
            . "IF(DATE({$specialFrom}) <= {$curentDate}, 1, 0)) > 0 AND IF({$specialTo} IS NULL, 1, "
            . "IF(DATE({$specialTo}) >= {$curentDate}, 1, 0)) > 0 AND {$specialPrice} < {$price}, "
            . "{$specialPrice}, {$price})");
        $select->columns(array(
            'orig_price'    => $price,
            'price'         => $finalPrice,
            'min_price'     => $finalPrice,
            'max_price'     => $finalPrice,
            'tier_price'    => new Zend_Db_Expr('tp.min_price'),
            'base_tier'     => new Zend_Db_Expr('tp.min_price'),
        ));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);

        /**
         * Add possibility modify prices from external events
         */
        $select = $write->select()
            ->join(array('wd' => $this->_getWebsiteDateTable()),
                'i.website_id = wd.website_id',
                array());
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', array(
            'index_table'       => array('i' => $this->_getDefaultFinalPriceTable()),
            'select'            => $select,
            'entity_id'         => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id'        => 'i.website_id',
            'website_date'      => 'wd.date',
            'update_fields'     => array('price', 'min_price', 'max_price')
        ));

        return $this;
    }

    /**
     * Retrieve table name for custom option temporary aggregation data
     *
     * @return string
     */
    protected function _getCustomOptionAggregateTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_option_aggregate_idx');
        }
        return $this->getTable('catalog/product_price_indexer_option_aggregate_idx');
    }

    /**
     * Retrieve table name for custom option prices data
     *
     * @return string
     */
    protected function _getCustomOptionPriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_option_idx');
        }
        return $this->getTable('catalog/product_price_indexer_option_tmp');
    }

    /**
     * Prepare table structure for custom option temporary aggregation data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareCustomOptionAggregateTable()
    {
        $this->_getWriteAdapter()->delete($this->_getCustomOptionAggregateTable());
        return $this;
    }

    /**
     * Prepare table structure for custom option prices data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareCustomOptionPriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getCustomOptionPriceTable());
        return $this;
    }

    /**
     * Apply custom option minimal and maximal price to temporary final price index table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _applyCustomOption()
    {
        $write      = $this->_getWriteAdapter();
        $coaTable   = $this->_getCustomOptionAggregateTable();
        $copTable   = $this->_getCustomOptionPriceTable();

        $this->_prepareCustomOptionAggregateTable();
        $this->_prepareCustomOptionPriceTable();

        $select = $write->select()
            ->from(
                array('i' => $this->_getDefaultFinalPriceTable()),
                array('entity_id', 'customer_group_id', 'website_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                'cw.website_id = i.website_id',
                array())
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.group_id = cw.default_group_id',
                array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'cs.store_id = csg.default_store_id',
                array())
            ->join(
                array('o' => $this->getTable('catalog/product_option')),
                'o.product_id = i.entity_id',
                array('option_id'))
            ->join(
                array('ot' => $this->getTable('catalog/product_option_type_value')),
                'ot.option_id = o.option_id',
                array())
            ->join(
                array('otpd' => $this->getTable('catalog/product_option_type_price')),
                'otpd.option_type_id = ot.option_type_id AND otpd.store_id = 0',
                array())
            ->joinLeft(
                array('otps' => $this->getTable('catalog/product_option_type_price')),
                'otps.option_type_id = otpd.option_type_id AND otpd.store_id = cs.store_id',
                array())
            ->group(array('i.entity_id', 'i.customer_group_id', 'i.website_id', 'o.option_id'));

        $minPrice = new Zend_Db_Expr("IF(o.is_require, MIN(IF(IF(otps.option_type_price_id>0, otps.price_type, "
            . "otpd.price_type)='fixed', IF(otps.option_type_price_id>0, otps.price, otpd.price), "
            . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), 0)");
        $tierPrice = new Zend_Db_Expr("IF(i.base_tier IS NOT NULL, IF(o.is_require, "
            . "MIN(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
            . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
            . "ROUND(i.base_tier * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), 0), NULL)");
        $maxPrice = new Zend_Db_Expr("IF((o.type='radio' OR o.type='drop_down'), "
            . "MAX(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
            . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
            . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), "
            . "SUM(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
            . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
            . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))))");

        $select->columns(array(
            'min_price'  => $minPrice,
            'max_price'  => $maxPrice,
            'tier_price' => $tierPrice
        ));

        $query = $select->insertFromSelect($coaTable);
        $write->query($query);

        $select = $write->select()
            ->from(
                array('i' => $this->_getDefaultFinalPriceTable()),
                array('entity_id', 'customer_group_id', 'website_id'))
            ->join(
                array('cw' => $this->getTable('core/website')),
                'cw.website_id = i.website_id',
                array())
            ->join(
                array('csg' => $this->getTable('core/store_group')),
                'csg.group_id = cw.default_group_id',
                array())
            ->join(
                array('cs' => $this->getTable('core/store')),
                'cs.store_id = csg.default_store_id',
                array())
            ->join(
                array('o' => $this->getTable('catalog/product_option')),
                'o.product_id = i.entity_id',
                array('option_id'))
            ->join(
                array('opd' => $this->getTable('catalog/product_option_price')),
                'opd.option_id = o.option_id AND opd.store_id = 0',
                array())
            ->joinLeft(
                array('ops' => $this->getTable('catalog/product_option_price')),
                'ops.option_id = opd.option_id AND ops.store_id = cs.store_id',
                array());

        $minPrice = new Zend_Db_Expr("IF((@price:=IF(IF(ops.option_price_id>0, ops.price_type, opd.price_type)='fixed',"
            . " IF(ops.option_price_id>0, ops.price, opd.price), ROUND(i.price * (IF(ops.option_price_id>0, "
            . "ops.price, opd.price) / 100), 4))) AND o.is_require, @price,0)");
        $maxPrice = new Zend_Db_Expr("@price");
        $tierPrice = new Zend_Db_Expr("IF(i.base_tier IS NOT NULL, IF((@tier_price:=IF(IF(ops.option_price_id>0, "
            . "ops.price_type, opd.price_type)='fixed', IF(ops.option_price_id>0, ops.price, opd.price), "
            . "ROUND(i.base_tier * (IF(ops.option_price_id>0, ops.price, opd.price) / 100), 4))) AND o.is_require, "
            . "@tier_price, 0), NULL)");

        $select->columns(array(
            'min_price'  => $minPrice,
            'max_price'  => $maxPrice,
            'tier_price' => $tierPrice
        ));

        $query = $select->insertFromSelect($coaTable);
        $write->query($query);

        $select = $write->select()
            ->from(
                array($coaTable),
                array(
                    'entity_id',
                    'customer_group_id',
                    'website_id',
                    'min_price'     => 'SUM(min_price)',
                    'max_price'     => 'SUM(max_price)',
                    'tier_price'    => 'SUM(tier_price)',
                ))
            ->group(array('entity_id', 'customer_group_id', 'website_id'));
        $query = $select->insertFromSelect($copTable);
        $write->query($query);

        $table  = array('i' => $this->_getDefaultFinalPriceTable());
        $select = $write->select()
            ->join(
                array('io' => $copTable),
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                    .' AND i.website_id = io.website_id',
                array());
        $select->columns(array(
            'min_price'  => new Zend_Db_Expr('i.min_price + io.min_price'),
            'max_price'  => new Zend_Db_Expr('i.max_price + io.max_price'),
            'tier_price' => new Zend_Db_Expr('IF(i.tier_price IS NOT NULL, i.tier_price + io.tier_price, NULL)'),
        ));
        $query = $select->crossUpdateFromSelect($table);
        $write->query($query);

        if ($this->useIdxTable()) {
            $write->truncate($coaTable);
            $write->truncate($copTable);
        }
        else {
            $write->delete($coaTable);
            $write->delete($copTable);
        }

        return $this;
    }

    /**
     * Mode Final Prices index to primary temporary index table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _movePriceDataToIndexTable()
    {
        $columns = array(
            'entity_id'         => 'entity_id',
            'customer_group_id' => 'customer_group_id',
            'website_id'        => 'website_id',
            'tax_class_id'      => 'tax_class_id',
            'price'             => 'orig_price',
            'final_price'       => 'price',
            'min_price'         => 'min_price',
            'max_price'         => 'max_price',
            'tier_price'        => 'tier_price'
        );

        $write  = $this->_getWriteAdapter();
        $table  = $this->_getDefaultFinalPriceTable();
        $select = $write->select()
            ->from($table, $columns);

        $query = $select->insertFromSelect($this->getIdxTable());
        $write->query($query);

        if ($this->useIdxTable()) {
            $write->truncate($table);
        }
        else {
            $write->delete($table);
        }

        return $this;
    }

    /**
     * Retrieve table name for product tier price index
     *
     * @return string
     */
    protected function _getTierPriceIndexTable()
    {
        return $this->getTable('catalog/product_index_tier_price');
    }

    /**
     * Register data required by product type process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    public function registerEvent(Mage_Index_Model_Event $event)
    {}

    /**
     * Retrieve temporary index table name
     *
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog/product_price_indexer_idx');
        }
        return $this->getTable('catalog/product_price_indexer_tmp');
    }
}
