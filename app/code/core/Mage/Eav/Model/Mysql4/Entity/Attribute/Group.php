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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Mysql4_Entity_Attribute_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_group', 'attribute_group_id');
    }

    /**
     * Checks if attribute group exists
     *
     * @param Mage_Eav_Model_Entity_Attribute_Group $object
     * @return boolean
     */
    public function itemExists($object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id = ?', $object->getAttributeSetId())
            ->where('attribute_group_name = ?', $object->getAttributeGroupName());
        if ($this->_getReadAdapter()->fetchRow($select)) {
            return true;
        }
        return false;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getSortOrder()) {
            $object->setSortOrder($this->_getMaxSortOrder($object) + 1);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
            }
        }
        return parent::_afterSave($object);
    }

    private function _getMaxSortOrder($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable(), new Zend_Db_Expr("MAX(`sort_order`)"))
            ->where("{$this->getMainTable()}.attribute_set_id = ?", $object->getAttributeSetId());
        $maxOrder = $read->fetchOne($select);
        return $maxOrder;
    }

    /**
     * Set any group default if old one was removed
     *
     * @param integer $attributeSetId
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Group
     */
    public function updateDefaultGroup($attributeSetId)
    {
        $this->_getWriteAdapter()->query(
            "UPDATE `{$this->getMainTable()}` SET default_id = 1 WHERE attribute_set_id = :attribute_set_id ORDER BY default_id DESC LIMIT 1",
            array('attribute_set_id' => $attributeSetId)
        );
        return $this;
    }
}
