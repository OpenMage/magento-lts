<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Sale_Collection extends Varien_Object implements IteratorAggregate
{
    /**
     * Read connection
     *
     * @var Varien_Db_Adapter_Interface|false
     */
    protected $_read;

    protected $_items = [];

    protected $_totals = ['lifetime' => 0, 'num_orders' => 0];

    /**
     * Entity attribute
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Collection's Zend_Db_Select object
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    public function __construct()
    {
        $this->_entity = Mage::getModel('sales_entity/order');
        $this->_read = $this->_entity->getReadConnection();
    }

    /**
     * @return $this
     */
    public function setCustomerFilter(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->_select = $this->_read->select();
        $entityTable = $this->getEntity()->getEntityTable();
        $paidTable  = $this->getAttribute('grand_total')->getBackend()->getTable();
        $idField    = $this->getEntity()->getIdFieldName();
        $this->getSelect()
            ->from(
                ['sales' => $entityTable],
                [
                    'store_id',
                    'lifetime'  => 'sum(sales.base_grand_total)',
                    'avgsale'   => 'avg(sales.base_grand_total)',
                    'num_orders' => 'count(sales.base_grand_total)'
                ]
            )
            ->where('sales.entity_type_id=?', $this->getEntity()->getTypeId())
            ->group('sales.store_id')
        ;
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            $this->getSelect()
                ->where('sales.customer_id=?', $this->_customer->getId());
        }

        $this->printLogQuery($printQuery, $logQuery);
        try {
            $values = $this->_read->fetchAll($this->getSelect()->__toString());
        } catch (Exception $e) {
            $this->printLogQuery(true, true, $this->getSelect()->__toString());
            throw $e;
        }
        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();
        if (!empty($values)) {
            foreach ($values as $v) {
                $obj = new Varien_Object($v);
                $storeName = $stores[$obj->getStoreId()] ?? null;

                $this->_items[ $v['store_id'] ] = $obj;
                $this->_items[ $v['store_id'] ]->setStoreName($storeName);
                $this->_items[ $v['store_id'] ]->setAvgNormalized($obj->getAvgsale() * $obj->getNumOrders());
                foreach ($this->_totals as $key => $value) {
                    $this->_totals[$key] += $obj->getData($key);
                }
            }
            if ($this->_totals['num_orders']) {
                $this->_totals['avgsale'] = $this->_totals['lifetime'] / $this->_totals['num_orders'];
            }
        }

        return $this;
    }

    /**
     * Print and/or log query
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @param null|string $sql
     * @return Mage_Sales_Model_Entity_Sale_Collection
     */
    public function printLogQuery($printQuery = false, $logQuery = false, $sql = null)
    {
        if ($printQuery) {
            echo is_null($sql) ? $this->getSelect()->__toString() : $sql;
        }

        if ($logQuery) {
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
     * @param string $attr
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attr)
    {
        return $this->_entity->getAttribute($attr);
    }

    /**
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @return ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * @return Varien_Object
     */
    public function getTotals()
    {
        return new Varien_Object($this->_totals);
    }
}
