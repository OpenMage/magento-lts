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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer group collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/group');
    }

    public function setTaxGroupFilter($classId)
    {
        $taxClassGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_group');
        $this->_select->joinLeft($taxClassGroupTable, "{$taxClassGroupTable}.class_group_id=main_table.customer_group_id");
        $this->_select->where("{$taxClassGroupTable}.class_parent_id = ?", $classId);
        return $this;
    }

    public function setIgnoreIdFilter($indexes)
    {
        if( !count($indexes) > 0 ) {
            return $this;
        }
        $this->_select->where('main_table.customer_group_id NOT IN(?)', $indexes);
        return $this;
    }

    public function setRealGroupsFilter()
    {
        $this->addFieldToFilter('customer_group_id', array('gt'=>0));
        return $this;
    }

    public function addTaxClass()
    {
        $taxClassTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class');
        $this->_select->joinLeft($taxClassTable, "main_table.tax_class_id = {$taxClassTable}.class_id");

        return $this;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }
    public function toOptionHash()
    {
        return parent::_toOptionHash('customer_group_id', 'customer_group_code');
    }
}