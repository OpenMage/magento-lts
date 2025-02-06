<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Dataflow batch collection
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Batch_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init model
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow/batch');
    }

    /**
     * Add expire filter (for abandoned batches)
     *
     */
    public function addExpireFilter()
    {
        $date = Mage::getSingleton('core/date');
        /** @var Mage_Core_Model_Date $date */
        $lifetime = Mage_Dataflow_Model_Batch::LIFETIME;
        $expire   = $date->gmtDate(null, $date->timestamp() - $lifetime);

        $this->getSelect()->where('created_at < ?', $expire);
    }
}
