<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report event type resource model
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Event_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/event_type', 'event_type_id');
    }
}
