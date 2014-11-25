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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog rule indexer
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Action_Index_Refresh
{
    /**
     * Connection instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Core factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource_Db_Abstract
     */
    protected $_resource;

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Constructor with parameters
     * Array of arguments with keys
     *  - 'connection' Varien_Db_Adapter_Interface
     *  - 'factory' Mage_Core_Model_Factory
     *  - 'resource' Mage_Core_Model_Resource_Db_Abstract
     *  - 'app' Mage_Core_Model_App
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_setConnection($args['connection']);
        $this->_setFactory($args['factory']);
        $this->_setResource($args['resource']);
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Set connection
     *
     * @param Varien_Db_Adapter_Interface $connection
     */
    protected function _setConnection(Varien_Db_Adapter_Interface $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Set factory
     *
     * @param Mage_Core_Model_Factory $factory
     */
    protected function _setFactory(Mage_Core_Model_Factory $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * Set resource
     *
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    protected function _setResource(Mage_Core_Model_Resource_Db_Abstract $resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Run reindex
     */
    public function execute()
    {
        $this->_app->dispatchEvent('catalogrule_before_apply', array('resource' => $this->_resource));

        /** @var $coreDate Mage_Core_Model_Date */
        $coreDate  = $this->_factory->getModel('core/date');
        $timestamp = $coreDate->gmtTimestamp('Today');

        foreach ($this->_app->getWebsites(false) as $website) {
            /** @var $website Mage_Core_Model_Website */
            if ($website->getDefaultStore()) {
                $this->_reindex($website, $timestamp);
            }
        }

        $this->_prepareGroupWebsite($timestamp);
        $this->_prepareAffectedProduct();
    }

    /**
     * Return temporary table name
     *
     * @return string
     */
    protected function _getTemporaryTable()
    {
        return $this->_resource->getTable('catalogrule/rule_product_price_tmp');
    }

    /**
     * Create temporary table
     */
    protected function _createTemporaryTable()
    {
        $this->_connection->dropTemporaryTable($this->_getTemporaryTable());
        $table = $this->_connection->newTable($this->_getTemporaryTable())
            ->addColumn(
                'grouped_id',
                Varien_Db_Ddl_Table::TYPE_VARCHAR,
                80,
                array(),
                'Grouped ID'
            )
            ->addColumn(
                'product_id',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true
                ),
                'Product ID'
            )
            ->addColumn(
                'customer_group_id',
                Varien_Db_Ddl_Table::TYPE_SMALLINT,
                5,
                array(
                    'unsigned' => true
                ),
                'Customer Group ID'
            )
            ->addColumn(
                'from_date',
                Varien_Db_Ddl_Table::TYPE_DATE,
                null,
                array(),
                'From Date'
            )
            ->addColumn(
                'to_date',
                Varien_Db_Ddl_Table::TYPE_DATE,
                null,
                array(),
                'To Date'
            )
            ->addColumn(
                'action_amount',
                Varien_Db_Ddl_Table::TYPE_DECIMAL,
                '12,4',
                array(),
                'Action Amount'
            )
            ->addColumn(
                'action_operator',
                Varien_Db_Ddl_Table::TYPE_VARCHAR,
                10,
                array(),
                'Action Operator'
            )
            ->addColumn(
                'action_stop',
                Varien_Db_Ddl_Table::TYPE_SMALLINT,
                6,
                array(),
                'Action Stop'
            )
            ->addColumn(
                'sort_order',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                10,
                array(
                    'unsigned' => true
                ),
                'Sort Order'
            )
            ->addColumn(
                'price',
                Varien_Db_Ddl_Table::TYPE_DECIMAL,
                '12,4',
                array(),
                'Product Price'
            )
            ->addColumn(
                'rule_product_id',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true
                ),
                'Rule Product ID'
            )
            ->addColumn(
                'from_time',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                    'default'  => 0,
                ),
                'From Time'
            )
            ->addColumn(
                'to_time',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                    'default'  => 0,
                ),
                'To Time'
            )
            ->addIndex(
                $this->_connection->getIndexName($this->_getTemporaryTable(), 'grouped_id'),
                array('grouped_id')
            )
            ->setComment('CatalogRule Price Temporary Table');
        $this->_connection->createTemporaryTable($table);
    }

    /**
     * Prepare temporary data
     *
     * @param Mage_Core_Model_Website $website
     * @return Varien_Db_Select
     */
    protected function _prepareTemporarySelect(Mage_Core_Model_Website $website)
    {
        /** @var $catalogFlatHelper Mage_Catalog_Helper_Product_Flat */
        $catalogFlatHelper = $this->_factory->getHelper('catalog/product_flat');

        /** @var $eavConfig Mage_Eav_Model_Config */
        $eavConfig = $this->_factory->getSingleton('eav/config');
        $priceAttribute = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price');

        $select = $this->_connection->select()
            ->from(
                array('rp' => $this->_resource->getTable('catalogrule/rule_product')),
                array()
            )
            ->joinInner(
                array('r' => $this->_resource->getTable('catalogrule/rule')),
                'r.rule_id = rp.rule_id',
                array()
            )
            ->where('rp.website_id = ?', $website->getId())
            ->order(
                array('rp.product_id', 'rp.customer_group_id', 'rp.sort_order', 'rp.rule_product_id')
            )
            ->joinLeft(
                array(
                    'pg' => $this->_resource->getTable('catalog/product_attribute_group_price')
                ),
                'pg.entity_id = rp.product_id AND pg.customer_group_id = rp.customer_group_id'
                    . ' AND pg.website_id = rp.website_id',
                array()
            )
            ->joinLeft(
                array(
                    'pgd' => $this->_resource->getTable('catalog/product_attribute_group_price')
                ),
                'pgd.entity_id = rp.product_id AND pgd.customer_group_id = rp.customer_group_id'
                    . ' AND pgd.website_id = 0',
                array()
            );

        $storeId = $website->getDefaultStore()->getId();

        if ($catalogFlatHelper->isEnabled() && $storeId && $catalogFlatHelper->isBuilt($storeId)) {
            $select->joinInner(
                array('p' => $this->_resource->getTable('catalog/product_flat') . '_' . $storeId),
                'p.entity_id = rp.product_id',
                array()
            );
            $priceColumn = $this->_connection->getIfNullSql(
                $this->_connection->getIfNullSql(
                    'pg.value',
                    'pgd.value'
                ),
                'p.price'
            );
        } else {
            $select->joinInner(
                    array(
                        'pd' => $this->_resource->getTable(array('catalog/product', $priceAttribute->getBackendType()))
                    ),
                    'pd.entity_id = rp.product_id AND pd.store_id = 0 AND pd.attribute_id = '
                        . $priceAttribute->getId(),
                    array()
                )
                ->joinLeft(
                    array(
                        'p' => $this->_resource->getTable(array('catalog/product', $priceAttribute->getBackendType()))
                    ),
                    'p.entity_id = rp.product_id AND p.store_id = ' . $storeId
                        . ' AND p.attribute_id = pd.attribute_id',
                    array()
                );
            $priceColumn = $this->_connection->getIfNullSql(
                $this->_connection->getIfNullSql(
                    'pg.value',
                    'pgd.value'
                ),
                $this->_connection->getIfNullSql(
                    'p.value',
                    'pd.value'
                )
            );
        }

        $select->columns(
            array(
                'grouped_id' => $this->_connection->getConcatSql(
                    array('rp.product_id', 'rp.customer_group_id'),
                    '-'
                ),
                'product_id'        => 'rp.product_id',
                'customer_group_id' => 'rp.customer_group_id',
                'from_date'         => 'r.from_date',
                'to_date'           => 'r.to_date',
                'action_amount'     => 'rp.action_amount',
                'action_operator'   => 'rp.action_operator',
                'action_stop'       => 'rp.action_stop',
                'sort_order'        => 'rp.sort_order',
                'price'             => $priceColumn,
                'rule_product_id'   => 'rp.rule_product_id',
                'from_time'         => 'rp.from_time',
                'to_time'           => 'rp.to_time'
            )
        );

        return $select;
    }

    /**
     * Prepare price column
     *
     * @return Zend_Db_Expr
     */
    protected function _calculatePrice()
    {
        $toPercent = $this->_connection->quote('to_percent');
        $byPercent = $this->_connection->quote('by_percent');
        $toFixed   = $this->_connection->quote('to_fixed');
        $byFixed   = $this->_connection->quote('by_fixed');
        $nA        = $this->_connection->quote('N/A');

        return $this->_connection->getCaseSql(
            '',
            array(
                $this->_connection->getIfNullSql(
                    new Zend_Db_Expr('@group_id'), $nA
                ) . ' != cppt.grouped_id' =>
                '@price := ' . $this->_connection->getCaseSql(
                    $this->_connection->quoteIdentifier('cppt.action_operator'),
                    array(
                        $toPercent => new Zend_Db_Expr('cppt.price * cppt.action_amount/100'),
                        $byPercent => new Zend_Db_Expr('cppt.price * (1 - cppt.action_amount/100)'),
                        $toFixed   => $this->_connection->getCheckSql(
                            new Zend_Db_Expr('cppt.action_amount < cppt.price'),
                            new Zend_Db_Expr('cppt.action_amount'),
                            new Zend_Db_Expr('cppt.price')
                        ),
                        $byFixed   => $this->_connection->getCheckSql(
                            new Zend_Db_Expr('0 > cppt.price - cppt.action_amount'),
                            new Zend_Db_Expr('0'),
                            new Zend_Db_Expr('cppt.price - cppt.action_amount')
                        ),
                    )
                ),
                $this->_connection->getIfNullSql(
                    new Zend_Db_Expr('@group_id'), $nA
                ) . ' = cppt.grouped_id AND '
                . $this->_connection->getIfNullSql(
                    new Zend_Db_Expr('@action_stop'),
                    new Zend_Db_Expr(0)
                ) . ' = 0' => '@price := ' . $this->_connection->getCaseSql(
                    $this->_connection->quoteIdentifier('cppt.action_operator'),
                    array(
                        $toPercent => new Zend_Db_Expr('@price * cppt.action_amount/100'),
                        $byPercent => new Zend_Db_Expr('@price * (1 - cppt.action_amount/100)'),
                        $toFixed   => $this->_connection->getCheckSql(
                            new Zend_Db_Expr('cppt.action_amount < @price'),
                            new Zend_Db_Expr('cppt.action_amount'),
                            new Zend_Db_Expr('@price')
                        ),
                        $byFixed   => $this->_connection->getCheckSql(
                            new Zend_Db_Expr('0 > @price - cppt.action_amount'),
                            new Zend_Db_Expr('0'),
                            new Zend_Db_Expr('@price - cppt.action_amount')
                        ),
                    )
                )
            ),
            '@price := @price'
        );
    }

    /**
     * Prepare index select
     *
     * @param Mage_Core_Model_Website $website
     * @param $time
     * @return Varien_Db_Select
     */
    protected function _prepareIndexSelect(Mage_Core_Model_Website $website, $time)
    {
        $nA = $this->_connection->quote('N/A');
        $this->_connection->query('SET @price := 0');
        $this->_connection->query('SET @group_id := NULL');
        $this->_connection->query('SET @action_stop := NULL');

        $indexSelect = $this->_connection->select()
            ->from(array('cppt' => $this->_getTemporaryTable()), array())
            ->order(array('cppt.grouped_id', 'cppt.sort_order', 'cppt.rule_product_id'))
            ->columns(
                array(
                    'customer_group_id' => 'cppt.customer_group_id',
                    'product_id'        => 'cppt.product_id',
                    'rule_price'        => $this->_calculatePrice(),
                    'latest_start_date' => 'cppt.from_date',
                    'earliest_end_date' => 'cppt.to_date',
                    new Zend_Db_Expr(
                        $this->_connection->getCaseSql(
                            '',
                            array(
                                $this->_connection->getIfNullSql(
                                    new Zend_Db_Expr('@group_id'),
                                    $nA
                                ) . ' != cppt.grouped_id' => new Zend_Db_Expr('@action_stop := cppt.action_stop'),
                                $this->_connection->getIfNullSql(
                                    new Zend_Db_Expr('@group_id'),
                                    $nA
                                ) . ' = cppt.grouped_id' => '@action_stop := '
                                    . $this->_connection->getIfNullSql(
                                        new Zend_Db_Expr('@action_stop'),
                                        new Zend_Db_Expr(0)
                                    ) . ' + cppt.action_stop',
                            )
                        )
                    ),
                    new Zend_Db_Expr('@group_id := cppt.grouped_id'),
                    'from_time'         => 'cppt.from_time',
                    'to_time'           => 'cppt.to_time'
                )
            );

        $select = $this->_connection->select()
            ->from($indexSelect, array())
            ->joinInner(
                array(
                    'dates' => $this->_connection->select()->union(
                        array(
                            new Zend_Db_Expr(
                                'SELECT ' . $this->_connection->getDateAddSql(
                                    $this->_connection->fromUnixtime($time),
                                    -1,
                                    Varien_Db_Adapter_Interface::INTERVAL_DAY
                                ) . ' AS rule_date'
                            ),
                            new Zend_Db_Expr('SELECT ' . $this->_connection->fromUnixtime($time) . ' AS rule_date'),
                            new Zend_Db_Expr(
                                'SELECT ' . $this->_connection->getDateAddSql(
                                    $this->_connection->fromUnixtime($time),
                                    1,
                                    Varien_Db_Adapter_Interface::INTERVAL_DAY
                                ) . ' AS rule_date'
                            ),
                        )
                    )
                ),
                '1=1',
                array()
            )
            ->columns(
                array(
                    'rule_product_price_id' => new Zend_Db_Expr('NULL'),
                    'rule_date'             => 'dates.rule_date',
                    'customer_group_id'     => 'customer_group_id',
                    'product_id'            => 'product_id',
                    'rule_price'            => 'MIN(rule_price)',
                    'website_id'            => new Zend_Db_Expr($website->getId()),
                    'latest_start_date'     => 'latest_start_date',
                    'earliest_end_date'     => 'earliest_end_date',
                )
            )
            ->where(new Zend_Db_Expr($this->_connection->getUnixTimestamp('dates.rule_date') . " >= from_time"))
            ->where(
                $this->_connection->getCheckSql(
                    new Zend_Db_Expr('to_time = 0'),
                    new Zend_Db_Expr(1),
                    new Zend_Db_Expr($this->_connection->getUnixTimestamp('dates.rule_date') . " <= to_time")
                )
            )
            ->group(array('customer_group_id', 'product_id', 'dates.rule_date'));

        return $select;
    }

    /**
     * Remove old index data
     *
     * @param Mage_Core_Model_Website $website
     */
    protected function _removeOldIndexData(Mage_Core_Model_Website $website)
    {
        $this->_connection->delete(
            $this->_resource->getTable('catalogrule/rule_product_price'),
            array('website_id = ?' => $website->getId())
        );
    }

    /**
     * Fill Index Data
     *
     * @param Mage_Core_Model_Website $website
     * @param int $time
     */
    protected function _fillIndexData(Mage_Core_Model_Website $website, $time)
    {
        $this->_connection->query(
            $this->_connection->insertFromSelect(
                $this->_prepareIndexSelect($website, $time),
                $this->_resource->getTable('catalogrule/rule_product_price')
            )
        );
    }

    /**
     * Reindex catalog prices by website for timestamp
     *
     * @param Mage_Core_Model_Website $website
     * @param int $timestamp
     */
    protected function _reindex(Mage_Core_Model_Website $website, $timestamp)
    {
        $this->_createTemporaryTable();
        $this->_connection->query(
            $this->_connection->insertFromSelect(
                $this->_prepareTemporarySelect($website),
                $this->_getTemporaryTable()
            )
        );
        $this->_removeOldIndexData($website);
        $this->_fillIndexData($website, $timestamp);
    }

    /**
     * Prepare data for group website relation
     */
    protected function _prepareGroupWebsite($timestamp)
    {
        $this->_connection->delete($this->_resource->getTable('catalogrule/rule_group_website'), array());
        $select = $this->_connection->select()
            ->distinct(true)
            ->from(
                $this->_resource->getTable('catalogrule/rule_product'),
                array('rule_id', 'customer_group_id', 'website_id')
            )
            ->where(new Zend_Db_Expr("{$timestamp} >= from_time"))
            ->where(
                $this->_connection->getCheckSql(
                    new Zend_Db_Expr('to_time = 0'),
                    new Zend_Db_Expr(1),
                    new Zend_Db_Expr("{$timestamp} <= to_time")
                )
            );
        $query = $select->insertFromSelect($this->_resource->getTable('catalogrule/rule_group_website'));
        $this->_connection->query($query);
    }

    /**
     * Return data for affected product
     *
     * @return null
     */
    protected function _getProduct()
    {
        return null;
    }

    /**
     * Prepare affected product
     */
    protected function _prepareAffectedProduct()
    {
        /** @var $modelCondition Mage_Catalog_Model_Product_Condition */
        $modelCondition = $this->_factory->getModel('catalog/product_condition');

        $productCondition = $modelCondition->setTable($this->_resource->getTable('catalogrule/affected_product'))
            ->setPkFieldName('product_id');

            $this->_app->dispatchEvent(
                'catalogrule_after_apply',
                array(
                    'product' => $this->_getProduct(),
                    'product_condition' => $productCondition
                )
            );

        $this->_connection->delete($this->_resource->getTable('catalogrule/affected_product'));
    }
}
