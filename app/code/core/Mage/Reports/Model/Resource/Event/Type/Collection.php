<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Report event types collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Event_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/event_type');
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('event_type_id', 'event_name');
    }
}
