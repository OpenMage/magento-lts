<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Inbox model
 *
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
