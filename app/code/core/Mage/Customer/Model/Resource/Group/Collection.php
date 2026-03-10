<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer group collection
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Group_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('customer/group');
    }

    /**
     * Set tax group filter
     *
     * @param  mixed $classId
     * @return $this
     */
    public function setTaxGroupFilter($classId)
    {
        $this->getSelect()->joinLeft(
            ['tax_class_group' => $this->getTable('tax/tax_class_group')],
            'tax_class_group.class_group_id = main_table.customer_group_id',
        );
        $this->addFieldToFilter('tax_class_group.class_parent_id', $classId);
        return $this;
    }

    /**
     * Set ignore ID filter
     *
     * @param  array $indexes
     * @return $this
     */
    public function setIgnoreIdFilter($indexes)
    {
        if (count($indexes)) {
            $this->addFieldToFilter('main_table.customer_group_id', ['nin' => $indexes]);
        }

        return $this;
    }

    /**
     * Set real groups filter
     *
     * @return $this
     */
    public function setRealGroupsFilter()
    {
        return $this->addFieldToFilter('customer_group_id', ['gt' => 0]);
    }

    /**
     * Add tax class
     *
     * @return $this
     */
    public function addTaxClass()
    {
        $this->getSelect()->joinLeft(
            ['tax_class_table' => $this->getTable('tax/tax_class')],
            'main_table.tax_class_id = tax_class_table.class_id',
        );
        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('customer_group_id', 'customer_group_code');
    }
}
