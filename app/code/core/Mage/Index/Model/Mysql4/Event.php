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
 * @package     Mage_Index
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Index_Model_Mysql4_Event extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('index/event', 'event_id');
    }

    /**
     * Check if semilar event exist before start saving data
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Index_Model_Mysql4_Event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * Check if event already exist and merge previous data
         */
        if (!$object->getId()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('type=?', $object->getType())
                ->where('entity=?', $object->getEntity());
            if ($object->hasEntityPk()) {
                $select->where('entity_pk=?', $object->getEntityPk());
            }
            $data = $this->_getWriteAdapter()->fetchRow($select);
            if ($data) {
                $object->mergePreviousData($data);
            }
        }
        return parent::_beforeSave($object);
    }

    /**
     * Save assigned processes
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Index_Model_Mysql4_Event
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $processIds = $object->getProcessIds();
        if (is_array($processIds)) {
            $processTable = $this->getTable('index/process_event');
            if (empty($processIds)) {
                $this->_getWriteAdapter()->delete($processTable);
            } else {
                foreach ($processIds as $processId => $processStatus) {
                    $data = array(
                        'process_id'=> $processId,
                        'event_id'  => $object->getId(),
                        'status'    => $processStatus
                    );
                    $this->_getWriteAdapter()->insertOnDuplicate($processTable, $data, array('status'));
                }
            }
        }
        return parent::_afterSave($object);
    }
}
