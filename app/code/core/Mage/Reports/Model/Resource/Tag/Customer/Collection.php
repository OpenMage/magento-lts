<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Report Customers Tags collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Tag_Customer_Collection extends Mage_Tag_Model_Resource_Customer_Collection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
    }
    /**
     * Add target count
     *
     * @return $this
     */
    public function addTagedCount()
    {
        $this->getSelect()
            ->columns(['taged' => 'COUNT(tr.tag_relation_id)']);
        return $this;
    }

    /**
     * get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns('COUNT(DISTINCT tr.customer_id)');

        return $countSelect;
    }
}
