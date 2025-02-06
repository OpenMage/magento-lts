<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Inbox model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Model_Resource_Inbox_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('adminnotification/inbox');
    }

    /**
     * Add remove filter
     *
     * @return $this
     */
    public function addRemoveFilter()
    {
        $this->getSelect()
            ->where('is_remove=?', 0);
        return $this;
    }
}
