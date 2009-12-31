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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Address EAV additional attribute resource collection
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Address_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
{
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType('customer_address')->getTypeId())
            ->join(
                array('additional_table' => $this->getTable('customer/eav_attribute')),
                'additional_table.attribute_id=main_table.attribute_id'
            );
        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param   int $typeId
     * @return  Mage_Customer_Model_Entity_Address_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Mage_Customer_Model_Entity_Address_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        $this->getSelect()->where('additional_table.is_visible=?', 1);
        return $this;
    }
}
