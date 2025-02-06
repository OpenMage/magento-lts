<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch abstract resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
abstract class Mage_Dataflow_Model_Resource_Batch_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Retrieve Id collection
     *
     * @return array
     */
    public function getIdCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return [];
        }

        $ids = [];
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), [$this->getIdFieldName()])
            ->where('batch_id = :batch_id');
        return $this->_getWriteAdapter()->fetchCol($select, ['batch_id' => $object->getBatchId()]);
    }

    /**
     * Delete current Batch collection
     *
     * @return Mage_Dataflow_Model_Resource_Batch_Abstract
     */
    public function deleteCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return $this;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), ['batch_id=?' => $object->getBatchId()]);
        return $this;
    }
}
