<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Dataflow batch collection
 *
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
