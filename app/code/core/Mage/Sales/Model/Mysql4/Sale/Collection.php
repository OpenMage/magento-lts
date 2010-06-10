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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Mysql4_Sale_Collection extends Varien_Object implements IteratorAggregate
{

    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Loaded collection items
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Totals data
     *
     * @var array
     */
    protected $_totals = array('lifetime' => 0, 'base_lifetime' => 0, 'base_avgsale' => 0, 'num_orders' => 0);

    /**
     * Entity attribute
     *
     * @var Mage_Sales_Model_Mysql4_Order
     */
    protected $_resource;

    /**
     * Collection's Zend_Db_Select object
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Customer model
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Order state value
     *
     * @var null|string|array
     */
    protected $_orderStateValue = null;

    /**
     * Order state condition
     *
     * @var string
     */
    protected $_orderStateCondition = null;

    /**
     * Set sales order entity and establish read connection
     *
     */
    public function __construct()
    {
        $this->_resource = Mage::getResourceSingleton('sales/order');
        $this->_read = $this->_resource->getReadConnection();
    }

    /**
     * Set filter by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Sales_Model_Mysql4_Sale_Collection
     */
    public function setCustomerFilter(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Add filter by stores
     *
     * @param array $storeIds
     * @return Mage_Sales_Model_Mysql4_Sale_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Set filter by order state
     *
     * @param string|array $state
     * @return Mage_Sales_Model_Mysql4_Sale_Collection
     */
    public function setOrderStateFilter($state, $exclude = false)
    {
        $this->_orderStateCondition = ($exclude) ? 'NOT IN' : 'IN';
        $this->_orderStateValue = (!is_array($state)) ? array($state) : $state;
        return $this;
    }

    /**
     * Load data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Sales_Model_Mysql4_Sale_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->_select = $this->_read->select();
        $this->getSelect()
            ->from(array('sales' => $this->_resource->getMainTable()),
                array(
                    'store_id',
                    'lifetime'      => 'sum(sales.base_grand_total)',
                    'base_lifetime' => 'sum(sales.base_grand_total * sales.base_to_global_rate)',
                    'avgsale'       => 'avg(sales.base_grand_total)',
                    'base_avgsale'  => 'avg(sales.base_grand_total * sales.base_to_global_rate)',
                    'num_orders'    => 'count(sales.base_grand_total)'
                )
            )
            ->group('sales.store_id')
        ;
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            $this->getSelect()
                ->where('sales.customer_id=?', $this->_customer->getId());
        }

        if (!is_null($this->_orderStateValue)) {
            $this->getSelect()->where('state ' . $this->_orderStateCondition . ' (?)', $this->_orderStateValue);
        }

        Mage::dispatchEvent('sales_sale_collection_query_before', array('collection' => $this));

        $this->printLogQuery($printQuery, $logQuery);
        try {
            $values = $this->_read->fetchAll($this->getSelect()->__toString());
        } catch (Exception $e) {
            $this->printLogQuery(true, true, $this->getSelect()->__toString());
            throw $e;
        }
        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();
        if (! empty($values)) {
            foreach ($values as $v) {
                $obj = new Varien_Object($v);
                $storeName = isset($stores[$obj->getStoreId()]) ? $stores[$obj->getStoreId()] : null;

                $this->_items[ $v['store_id'] ] = $obj;
                $this->_items[ $v['store_id'] ]->setStoreName($storeName);
                $this->_items[ $v['store_id'] ]->setWebsiteId(Mage::app()->getStore($obj->getStoreId())->getWebsiteId());
                $this->_items[ $v['store_id'] ]->setAvgNormalized($obj->getAvgsale() * $obj->getNumOrders());
                foreach ($this->_totals as $key => $value) {
                    $this->_totals[$key] += $obj->getData($key);
                }
            }
            if ($this->_totals['num_orders']) {
                $this->_totals['avgsale'] = $this->_totals['base_lifetime'] / $this->_totals['num_orders'];
            }
        }

        return $this;
    }

    /**
     * Print and/or log query
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @param mixed $sql
     * @return Mage_Sales_Model_Mysql4_Sale_Collection
     */
    public function printLogQuery($printQuery = false, $logQuery = false, $sql = null) {
        if ($printQuery) {
            echo is_null($sql) ? $this->getSelect()->__toString() : $sql;
        }

        if ($logQuery){
            Mage::log(is_null($sql) ? $this->getSelect()->__toString() : $sql);
        }
        return $this;
    }

    /**
     * Get zend db select instance
     *
     * @return Zend_Db_Select
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * Retrieve attribute entity by specified parameter
     *
     * @param int|string|object $attr
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attr)
    {
        return $this->_entity->getAttribute($attr);
    }

    /**
     * Retrieve currently used entity
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Retrieve Iterator instance of items array
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    /**
     * Retrieve array of items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve totals data converted into Varien_Object
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        return new Varien_Object($this->_totals);
    }

}
