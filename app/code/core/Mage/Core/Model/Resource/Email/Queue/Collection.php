<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Queue resource collection
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Email_Queue_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('core/email_queue');
    }

    /**
     * Add recipients and unserialize message parameters
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        return $this;
    }

    /**
     * Add filter by only ready for sending item
     *
     * @return $this
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()->where('main_table.processed_at IS NULL');
        return $this;
    }
}
