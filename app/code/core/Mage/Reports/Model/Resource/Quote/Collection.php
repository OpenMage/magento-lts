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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports quote collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Quote_Collection extends Mage_Sales_Model_Resource_Quote_Collection
{

    const SELECT_COUNT_SQL_TYPE_CART = 1;

    protected $_selectCountSqlType = 0;

    /**
     * Join fields
     *
     * @var array
     */
    protected $_joinedFields     = array();

    /**
     * Map
     *
     * @var array
     */
    protected $_map              = array('fields' => array('store_id' => 'main_table.store_id'));

    /**
     * Set type for COUNT SQL select
     *
     * @param int $type
     * @return Mage_Reports_Model_Mysql4_Quote_Collection
     */
    public function setSelectCountSqlType($type)
    {
        $this->_selectCountSqlType = $type;
        return $this;
    }

    protected function _construct()
    {
        parent::_construct();
        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;
    }


    /**
     * Prepare for abandoned report
     *
     * @param array $storeIds
     * @param string $filter
     * @return Mage_Reports_Model_Resource_Quote_Collection
     */
    public function prepareForAbandonedReport($storeIds, $filter = null)
    {
        $this->addFieldToFilter('items_count', array('neq' => '0'))
            ->addFieldToFilter('main_table.is_active', '1')
            ->addSubtotal($storeIds, $filter)
            ->addCustomerData($filter)
            ->setOrder('updated_at');
        if (is_array($storeIds) && !empty($storeIds)) {
            $this->addFieldToFilter('store_id', array('in' => $storeIds));
        }


        return $this;
    }

    /**
     * Prepare select query for products in carts report
     *
     * @return Mage_Reports_Model_Resource_Quote_Collection
     */
    public function prepareForProductsInCarts()
    {
        $productEntity          = Mage::getResourceSingleton('catalog/product_collection');
        $productAttrName        = $productEntity->getAttribute('name');
        $productAttrNameId      = (int) $productAttrName->getAttributeId();
        $productAttrNameTable   = $productAttrName->getBackend()->getTable();
        $productAttrPrice       = $productEntity->getAttribute('price');
        $productAttrPriceId     = (int) $productAttrPrice->getAttributeId();
        $productAttrPriceTable  = $productAttrPrice->getBackend()->getTable();

        $ordersSubSelect = clone $this->getSelect();
        $ordersSubSelect->reset()
            ->from(
                array('oi' => $this->getTable('sales/order_item')),
                array(
                   'orders' => new Zend_Db_Expr('COUNT(1)'),
                   'product_id'))
            ->group('oi.product_id');

        $this->getSelect()
            ->useStraightJoin(true)
            ->reset(Zend_Db_Select::COLUMNS)
            ->joinInner(
                array('quote_items' => $this->getTable('sales/quote_item')),
                'quote_items.quote_id = main_table.entity_id',
                null)
            ->joinInner(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id = quote_items.product_id',
                null)
            ->joinInner(
                array('product_name' => $productAttrNameTable),
                "product_name.entity_id = e.entity_id AND product_name.attribute_id = {$productAttrNameId}",
                array('name'=>'product_name.value'))
            ->joinInner(
                array('product_price' => $productAttrPriceTable),
                "product_price.entity_id = e.entity_id AND product_price.attribute_id = {$productAttrPriceId}",
                array('price' => new Zend_Db_Expr('product_price.value * main_table.base_to_global_rate')))
            ->joinLeft(
                array('order_items' => new Zend_Db_Expr(sprintf('(%s)', $ordersSubSelect))),
                'order_items.product_id = e.entity_id',
                array()
            )
            ->columns('e.*')
            ->columns(array('carts' => new Zend_Db_Expr('COUNT(quote_items.item_id)')))
            ->columns('order_items.orders')
            ->where('main_table.is_active = ?', 1)
            ->group('quote_items.product_id');

        return $this;
    }

    /**
     * Add store ids to filter
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Quote_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter('store_id', array('in' => $storeIds));
        return $this;
    }

    /**
     * Add customer data
     *
     * @param unknown_type $filter
     * @return Mage_Reports_Model_Resource_Quote_Collection
     */
    public function addCustomerData($filter = null)
    {
        $customerEntity          = Mage::getResourceSingleton('customer/customer');
        $attrFirstname           = $customerEntity->getAttribute('firstname');
        $attrFirstnameId         = (int) $attrFirstname->getAttributeId();
        $attrFirstnameTableName  = $attrFirstname->getBackend()->getTable();

        $attrLastname            = $customerEntity->getAttribute('lastname');
        $attrLastnameId          = (int) $attrLastname->getAttributeId();
        $attrLastnameTableName   = $attrLastname->getBackend()->getTable();

        $attrMiddlename          = $customerEntity->getAttribute('middlename');
        $attrMiddlenameId        = (int) $attrMiddlename->getAttributeId();
        $attrMiddlenameTableName = $attrMiddlename->getBackend()->getTable();

        $attrEmail       = $customerEntity->getAttribute('email');
        $attrEmailTableName = $attrEmail->getBackend()->getTable();

        $adapter = $this->getSelect()->getAdapter();
        $customerName = $adapter->getConcatSql(array('cust_fname.value', 'cust_mname.value', 'cust_lname.value',), ' ');
        $this->getSelect()
            ->joinInner(
                array('cust_email' => $attrEmailTableName),
                'cust_email.entity_id = main_table.customer_id',
                array('email' => 'cust_email.email')
            )
            ->joinInner(
                array('cust_fname' => $attrFirstnameTableName),
                implode(' AND ', array(
                    'cust_fname.entity_id = main_table.customer_id',
                    $adapter->quoteInto('cust_fname.attribute_id = ?', (int) $attrFirstnameId),
                )),
                array('firstname' => 'cust_fname.value')
            )
            ->joinInner(
                array('cust_mname' => $attrMiddlenameTableName),
                implode(' AND ', array(
                    'cust_mname.entity_id = main_table.customer_id',
                    $adapter->quoteInto('cust_mname.attribute_id = ?', (int) $attrMiddlenameId),
                )),
                array('middlename' => 'cust_mname.value')
            )
            ->joinInner(
                array('cust_lname' => $attrLastnameTableName),
                implode(' AND ', array(
                    'cust_lname.entity_id = main_table.customer_id',
                     $adapter->quoteInto('cust_lname.attribute_id = ?', (int) $attrLastnameId)
                )),
                array(
                    'lastname'      => 'cust_lname.value',
                    'customer_name' => $customerName
                )
            );

        $this->_joinedFields['customer_name'] = $customerName;
        $this->_joinedFields['email']         = 'cust_email.email';

        if ($filter) {
            if (isset($filter['customer_name'])) {
                $likeExpr = '%' . $filter['customer_name'] . '%';
                $this->getSelect()->where($this->_joinedFields['customer_name'] . ' LIKE ?', $likeExpr);
            }
            if (isset($filter['email'])) {
                $likeExpr = '%' . $filter['email'] . '%';
                $this->getSelect()->where($this->_joinedFields['email'] . ' LIKE ?', $likeExpr);
            }
        }

        return $this;
    }

    /**
     * Add subtotals
     *
     * @param array $storeIds
     * @param array $filter
     * @return Mage_Reports_Model_Resource_Quote_Collection
     */
    public function addSubtotal($storeIds = '', $filter = null)
    {
        if (is_array($storeIds)) {
            $this->getSelect()->columns(array(
                'subtotal' => '(main_table.base_subtotal_with_discount*main_table.base_to_global_rate)'
            ));
            $this->_joinedFields['subtotal'] =
                '(main_table.base_subtotal_with_discount*main_table.base_to_global_rate)';
        } else {
            $this->getSelect()->columns(array('subtotal' => 'main_table.base_subtotal_with_discount'));
            $this->_joinedFields['subtotal'] = 'main_table.base_subtotal_with_discount';
        }

        if ($filter && is_array($filter) && isset($filter['subtotal'])) {
            if (isset($filter['subtotal']['from'])) {
                $this->getSelect()->where(
                    $this->_joinedFields['subtotal'] . ' >= ?',
                    $filter['subtotal']['from'], Zend_Db::FLOAT_TYPE
                );
            }
            if (isset($filter['subtotal']['to'])) {
                $this->getSelect()->where(
                    $this->_joinedFields['subtotal'] . ' <= ?',
                    $filter['subtotal']['to'], Zend_Db::FLOAT_TYPE
                );
            }
        }

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return unknown
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->resetJoinLeft();

        if ($this->_selectCountSqlType == self::SELECT_COUNT_SQL_TYPE_CART) {
            $countSelect->columns("COUNT(DISTINCT e.entity_id)");
        } else {
            $countSelect->columns("COUNT(DISTINCT main_table.entity_id)");
        }

        return $countSelect;
    }
}
