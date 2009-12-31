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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports quote collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Quote_Collection extends Mage_Sales_Model_Mysql4_Quote_Collection
{
    protected $_joinedFields = array();

    protected $_map = array('fields' => array('store_id' => 'main_table.store_id'));

    public function prepareForAbandonedReport($storeIds, $filter = null)
    {
        $this->addFieldToFilter('items_count', array('neq' => '0'))
            ->addFieldToFilter('main_table.is_active', '1')
            ->addSubtotal($storeIds, $filter)
            ->addCustomerData($filter)
            ->setOrder('updated_at');
        if (is_array($storeIds)) {
            $this->addFieldToFilter('store_id', array('in' => $storeIds));
        }
        return $this;
    }

    /**
     * Add store ids to filter
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Mysql4_Quote_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter('store_id', array('in' => $storeIds));
        return $this;
    }

    public function addCustomerData($filter = null)
    {
        $customerEntity = Mage::getResourceSingleton('customer/customer');
        $attrFirstname = $customerEntity->getAttribute('firstname');
        $attrFirstnameId = $attrFirstname->getAttributeId();
        $attrFirstnameTableName = $attrFirstname->getBackend()->getTable();

        $attrLastname = $customerEntity->getAttribute('lastname');
        $attrLastnameId = $attrLastname->getAttributeId();
        $attrLastnameTableName = $attrLastname->getBackend()->getTable();

        $attrEmail = $customerEntity->getAttribute('email');
        $attrEmailTableName = $attrEmail->getBackend()->getTable();

        $this->getSelect()
            ->joinInner(
                array('cust_email'=>$attrEmailTableName),
                'cust_email.entity_id=main_table.customer_id',
                array('email'=>'cust_email.email')
            )
            ->joinInner(
                array('cust_fname'=>$attrFirstnameTableName),
                'cust_fname.entity_id=main_table.customer_id and cust_fname.attribute_id='.$attrFirstnameId,
                array('firstname'=>'cust_fname.value')
            )
            ->joinInner(
                array('cust_lname'=>$attrLastnameTableName),
                'cust_lname.entity_id=main_table.customer_id and cust_lname.attribute_id='.$attrLastnameId,
                array(
                    'lastname'=>'cust_lname.value',
                    'customer_name' => new Zend_Db_Expr('CONCAT(cust_fname.value, " ", cust_lname.value)')
                )
            );

        $this->_joinedFields['customer_name'] = 'CONCAT(cust_fname.value, " ", cust_lname.value)';
        $this->_joinedFields['email'] = 'cust_email.email';

        if ($filter) {
            if (isset($filter['customer_name'])) {
                $this->getSelect()->where($this->_joinedFields['customer_name'] . ' LIKE "%' . $filter['customer_name'] . '%"');
            }
            if (isset($filter['email'])) {
                $this->getSelect()->where($this->_joinedFields['email'] . ' LIKE "%' . $filter['email'] . '%"');
            }
        }

        return $this;
    }

    public function addSubtotal($storeIds = '', $filter = null)
    {
        if (is_array($storeIds)) {
            $this->getSelect()->columns(array("subtotal" => "(main_table.base_subtotal_with_discount*main_table.base_to_global_rate)"));
            $this->_joinedFields['subtotal'] = '(main_table.base_subtotal_with_discount*main_table.base_to_global_rate)';
        } else {
            $this->getSelect()->columns(array("subtotal" => "main_table.base_subtotal_with_discount"));
            $this->_joinedFields['subtotal'] = 'main_table.base_subtotal_with_discount';
        }

        if ($filter && is_array($filter) && isset($filter['subtotal'])) {
            if (isset($filter['subtotal']['from'])) {
                $this->getSelect()->where($this->_joinedFields['subtotal'] . ' >= ' . $filter['subtotal']['from']);
            }
            if (isset($filter['subtotal']['to'])) {
                $this->getSelect()->where($this->_joinedFields['subtotal'] . ' <= ' . $filter['subtotal']['to']);
            }
        }

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);

        $countSelect->columns("count(DISTINCT main_table.entity_id)");
        $sql = $countSelect->__toString();

        return $sql;
    }
}
