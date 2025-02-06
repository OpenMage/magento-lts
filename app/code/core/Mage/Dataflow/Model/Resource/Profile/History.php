<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert history resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Profile_History extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/profile_history', 'history_id');
    }

    /**
     * Sets up performed at time if needed
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getPerformedAt()) {
            $object->setPerformedAt($this->formatDate(time()));
        }
        parent::_beforeSave($object);
        return $this;
    }
}
