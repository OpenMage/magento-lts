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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Mysql4_Entity_Attribute_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/attribute_group', 'attribute_group_id');
    }

    public function itemExists($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where("attribute_group_name='{$object->getAttributeGroupName()}'");
        $data = $read->fetchRow($select);
        if (!$data) {
            return false;
        }
        return true;
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
}