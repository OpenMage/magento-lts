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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Entity_Sale_Collection extends Varien_Object implements IteratorAggregate
{

    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    protected $_items = array();

    protected $_totals = array('lifetime' => 0, 'num_orders' => 0);

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
     * Enter description here...
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    public function __construct()
    {
        $this->_entity = Mage::getModel('sales_entity/order');
        $this->_read = $this->_entity->getReadConnection();
    }

    public function setCustomerFilter(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        $this->_select = $this->_read->select();
        $entityTable= $this->getEntity()->getEntityTable();
        $paidTable  = $this->getAttribute('grand_total')->getBackend()->getTable();
        $idField    = $this->getEntity()->getIdFieldName();
        $this->getSelect()
            ->from(array('sales' => $entityTable),
                array(
                    'store_id',
                    'lifetime'  => 'sum(sales.base_grand_total)',
                    'avgsale'   => 'avg(sales.base_grand_total)',
                    'num_orders'=> 'count(sales.base_grand_total)'
                )
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
        if (! empty($values)) {
            foreach ($values as $v) {
                $obj = new Varien_Object($v);
                $storeName = isset($stores[$obj->getStoreId()]) ? $stores[$obj->getStoreId()] : null;

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
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return  Mage_Sales_Model_Entity_Order_Attribute_Collection_Paid
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
     * Enter description here...
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attr)
    {
        return $this->_entity->getAttribute($attr);
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Enter description here...
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Enter description here...
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        return new Varien_Object($this->_totals);
    }

}
