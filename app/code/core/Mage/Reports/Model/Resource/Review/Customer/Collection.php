<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Customers Review collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Review_Customer_Collection extends Mage_Review_Model_Resource_Review_Collection
{
    /**
     * Join customers
     *
     * @return $this
     */
    public function joinCustomers()
    {
        /**
         * Allow to use analytic function to result select
         */
        $this->_useAnalyticFunction = true;

        /** @var Varien_Db_Adapter_Interface $adapter */
        $adapter            = $this->getConnection();
        /** @var Mage_Customer_Model_Resource_Customer $customer */
        $customer           = Mage::getResourceSingleton('customer/customer');
        /** @var Mage_Eav_Model_Entity_Attribute $firstnameAttr */
        $firstnameAttr      = $customer->getAttribute('firstname');
        /** @var Mage_Eav_Model_Entity_Attribute $firstnameAttr */
        $middlenameAttr      = $customer->getAttribute('middlename');
        /** @var Mage_Eav_Model_Entity_Attribute $lastnameAttr */
        $lastnameAttr       = $customer->getAttribute('lastname');

        $firstnameCondition = ['table_customer_firstname.entity_id = detail.customer_id'];

        if ($firstnameAttr->getBackend()->isStatic()) {
            $firstnameField = 'firstname';
        } else {
            $firstnameField = 'value';
            $firstnameCondition[] = $adapter->quoteInto(
                'table_customer_firstname.attribute_id = ?',
                (int) $firstnameAttr->getAttributeId(),
            );
        }

        $this->getSelect()->joinInner(
            ['table_customer_firstname' => $firstnameAttr->getBackend()->getTable()],
            implode(' AND ', $firstnameCondition),
            [],
        );

        $middlenameCondition = ['table_customer_middlename.entity_id = detail.customer_id'];

        if ($middlenameAttr->getBackend()->isStatic()) {
            $middlenameField = 'middlename';
        } else {
            $middlenameField = 'value';
            $middlenameCondition[] = $adapter->quoteInto(
                'table_customer_middlename.attribute_id = ?',
                (int) $middlenameAttr->getAttributeId(),
            );
        }

        $this->getSelect()->joinInner(
            ['table_customer_middlename' => $middlenameAttr->getBackend()->getTable()],
            implode(' AND ', $middlenameCondition),
            [],
        );

        $lastnameCondition  = ['table_customer_lastname.entity_id = detail.customer_id'];
        if ($lastnameAttr->getBackend()->isStatic()) {
            $lastnameField = 'lastname';
        } else {
            $lastnameField = 'value';
            $lastnameCondition[] = $adapter->quoteInto(
                'table_customer_lastname.attribute_id = ?',
                (int) $lastnameAttr->getAttributeId(),
            );
        }

        //Prepare fullname field result
        $customerFullname = $adapter->getConcatSql([
            "table_customer_firstname.{$firstnameField}",
            "table_customer_middlename.{$middlenameField}",
            "table_customer_lastname.{$lastnameField}",
        ], ' ');
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->joinInner(
                ['table_customer_lastname' => $lastnameAttr->getBackend()->getTable()],
                implode(' AND ', $lastnameCondition),
                [],
            )
            ->columns([
                'customer_id' => 'detail.customer_id',
                'customer_name' => $customerFullname,
                'review_cnt'    => 'COUNT(main_table.review_id)'])
            ->group('detail.customer_id');

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns(new Zend_Db_Expr('COUNT(DISTINCT detail.customer_id)'));

        return  $countSelect;
    }
}
