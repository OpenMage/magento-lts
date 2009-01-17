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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Report Customers Review collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Review_Customer_Collection extends Mage_Review_Model_Mysql4_Review_Collection
{
    public function joinCustomers()
    {
        $customer = Mage::getResourceSingleton('customer/customer');
        //TODO: add full name logic
        $firstnameAttr = $customer->getAttribute('firstname');
        $firstnameAttrId = $firstnameAttr->getAttributeId();
        $firstnameTable = $firstnameAttr->getBackend()->getTable();

        if ($firstnameAttr->getBackend()->isStatic()) {
            $firstnameField = 'firstname';
            $attrCondition = '';
        } else {
            $firstnameField = 'value';
            $attrCondition = ' AND _table_customer_firstname.attribute_id = '.$firstnameAttrId;
        }

        $this->getSelect()->joinInner(array('_table_customer_firstname' => $firstnameTable),
            '_table_customer_firstname.entity_id=detail.customer_id'.$attrCondition, array());

        $lastnameAttr = $customer->getAttribute('lastname');
        $lastnameAttrId = $lastnameAttr->getAttributeId();
        $lastnameTable = $lastnameAttr->getBackend()->getTable();

        if ($lastnameAttr->getBackend()->isStatic()) {
            $lastnameField = 'lastname';
            $attrCondition = '';
        } else {
            $lastnameField = 'value';
            $attrCondition = ' AND _table_customer_lastname.attribute_id = '.$lastnameAttrId;
        }

        $this->getSelect()->joinInner(array('_table_customer_lastname' => $lastnameTable),
            '_table_customer_lastname.entity_id=detail.customer_id'.$attrCondition, array())
            ->from("", array(
                        'customer_name' => "CONCAT(_table_customer_firstname.{$firstnameField}, ' ', _table_customer_lastname.{$lastnameField})",
                        'review_cnt' => "COUNT(main_table.review_id)"))
            ->group('detail.customer_id');

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();

        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(DISTINCT `detail`.`customer_id`) from ', $sql);

        return $sql;
    }
}