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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Website extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/website', 'website_id');
        $this->_uniqueFields = array(array('field' => 'code', 'title' => Mage::helper('core')->__('Website with the same code')));
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Website
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!preg_match('/^[a-z]+[a-z0-9_]*$/', $object->getCode())) {
            Mage::throwException(Mage::helper('core')->__('Website code should contain only letters (a-z), numbers (0-9) or underscore(_), first character should be a letter'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Website
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getIsDefault()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('is_default' => 0),
                1
            );
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('is_default' => 1),
                $this->_getWriteAdapter()->quoteInto('website_id=?', $object->getId())
            );
        }
        return parent::_afterSave($object);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $model)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('core/config_data'),
            $this->_getWriteAdapter()->quoteInto("scope = 'websites' AND scope_id = ?", $model->getWebsiteId())
        );
        return $this;
    }
}